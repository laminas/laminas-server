<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Method;

use Laminas\Server\Method;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Test class for \Laminas\Server\Method\Definition
 *
 * @group      Laminas_Server
 */
class DefinitionTest extends TestCase
{
    /** @var Method\Definition */
    private $definition;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->definition = new Method\Definition();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testCallbackShouldBeNullByDefault(): void
    {
        $this->assertNull($this->definition->getCallback());
    }

    public function testSetCallbackShouldAcceptMethodCallback(): void
    {
        $callback = new Method\Callback();
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback();
        $this->assertSame($callback, $test);
    }

    public function testSetCallbackShouldAcceptArray(): void
    {
        $callback = [
            'type'     => 'function',
            'function' => 'foo',
        ];
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback()->toArray();
        $this->assertSame($callback, $test);
    }

    public function testMethodHelpShouldBeEmptyStringByDefault(): void
    {
        $this->assertEquals('', $this->definition->getMethodHelp());
    }

    public function testMethodHelpShouldBeMutable(): void
    {
        $this->assertEquals('', $this->definition->getMethodHelp());
        $this->definition->setMethodHelp('foo bar');
        $this->assertEquals('foo bar', $this->definition->getMethodHelp());
    }

    public function testNameShouldBeNullByDefault(): void
    {
        $this->assertNull($this->definition->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->assertNull($this->definition->getName());
        $this->definition->setName('foo.bar');
        $this->assertEquals('foo.bar', $this->definition->getName());
    }

    public function testObjectShouldBeNullByDefault(): void
    {
        $this->assertNull($this->definition->getObject());
    }

    public function testObjectShouldBeMutable(): void
    {
        $this->assertNull($this->definition->getObject());
        $object = new stdClass();
        $this->definition->setObject($object);
        $this->assertEquals($object, $this->definition->getObject());
    }

    public function testInvokeArgumentsShouldBeEmptyArrayByDefault(): void
    {
        $args = $this->definition->getInvokeArguments();
        $this->assertEmpty($args);
    }

    public function testInvokeArgumentsShouldBeMutable(): void
    {
        $this->testInvokeArgumentsShouldBeEmptyArrayByDefault();
        $args = ['foo', ['bar', 'baz'], new stdClass()];
        $this->definition->setInvokeArguments($args);
        $this->assertSame($args, $this->definition->getInvokeArguments());
    }

    public function testPrototypesShouldBeEmptyArrayByDefault(): void
    {
        $prototypes = $this->definition->getPrototypes();
        $this->assertEmpty($prototypes);
    }

    public function testDefinitionShouldAllowAddingSinglePrototypes(): void
    {
        $this->testPrototypesShouldBeEmptyArrayByDefault();
        $prototype1 = new Method\Prototype();
        $this->definition->addPrototype($prototype1);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);

        $prototype2 = new Method\Prototype();
        $this->definition->addPrototype($prototype2);
        $test = $this->definition->getPrototypes();
        $this->assertSame($prototype1, $test[0]);
        $this->assertSame($prototype2, $test[1]);
    }

    public function testDefinitionShouldAllowAddingMultiplePrototypes(): void
    {
        $prototype1 = new Method\Prototype();
        $prototype2 = new Method\Prototype();
        $prototypes = [$prototype1, $prototype2];
        $this->definition->addPrototypes($prototypes);
        $this->assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testSetPrototypesShouldOverwriteExistingPrototypes(): void
    {
        $this->testDefinitionShouldAllowAddingMultiplePrototypes();

        $prototype1 = new Method\Prototype();
        $prototype2 = new Method\Prototype();
        $prototypes = [$prototype1, $prototype2];
        $this->assertNotSame($prototypes, $this->definition->getPrototypes());
        $this->definition->setPrototypes($prototypes);
        $this->assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testDefintionShouldSerializeToArray(): void
    {
        $name       = 'foo.bar';
        $callback   = ['function' => 'foo', 'type' => 'function'];
        $prototypes = [['returnType' => 'struct', 'parameters' => ['string', 'array']]];
        $methodHelp = 'foo bar';
        $object     = new stdClass();
        $invokeArgs = ['foo', ['bar', 'baz']];
        $this->definition->setName($name)
                         ->setCallback($callback)
                         ->setPrototypes($prototypes)
                         ->setMethodHelp($methodHelp)
                         ->setObject($object)
                         ->setInvokeArguments($invokeArgs);
        $test = $this->definition->toArray();
        $this->assertEquals($name, $test['name']);
        $this->assertEquals($callback, $test['callback']);
        $this->assertEquals($prototypes, $test['prototypes']);
        $this->assertEquals($methodHelp, $test['methodHelp']);
        $this->assertEquals($object, $test['object']);
        $this->assertEquals($invokeArgs, $test['invokeArguments']);
    }

    public function testPassingOptionsToConstructorShouldSetObjectState(): void
    {
        $options    = [
            'name'            => 'foo.bar',
            'callback'        => ['function' => 'foo', 'type' => 'function'],
            'prototypes'      => [['returnType' => 'struct', 'parameters' => ['string', 'array']]],
            'methodHelp'      => 'foo bar',
            'object'          => new stdClass(),
            'invokeArguments' => ['foo', ['bar', 'baz']],
        ];
        $definition = new Method\Definition($options);
        $test       = $definition->toArray();
        $this->assertEquals($options['name'], $test['name']);
        $this->assertEquals($options['callback'], $test['callback']);
        $this->assertEquals($options['prototypes'], $test['prototypes']);
        $this->assertEquals($options['methodHelp'], $test['methodHelp']);
        $this->assertEquals($options['object'], $test['object']);
        $this->assertEquals($options['invokeArguments'], $test['invokeArguments']);
    }
}
