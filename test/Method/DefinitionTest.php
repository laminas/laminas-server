<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Method;

use Laminas\Server\Method\Callback;
use Laminas\Server\Method\Definition;
use Laminas\Server\Method\Prototype;
use PHPUnit\Framework\TestCase;
use stdClass;

class DefinitionTest extends TestCase
{
    private Definition $definition;

    protected function setUp(): void
    {
        $this->definition = new Definition();
    }

    public function testCallbackShouldBeNullByDefault(): void
    {
        self::assertNull($this->definition->getCallback());
    }

    public function testSetCallbackShouldAcceptMethodCallback(): void
    {
        $callback = new Callback();
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback();
        self::assertSame($callback, $test);
    }

    public function testSetCallbackShouldAcceptArray(): void
    {
        $callback = [
            'type'     => 'function',
            'function' => 'foo',
        ];
        $this->definition->setCallback($callback);
        $test = $this->definition->getCallback()->toArray();
        self::assertSame($callback, $test);
    }

    public function testMethodHelpShouldBeEmptyStringByDefault(): void
    {
        self::assertEquals('', $this->definition->getMethodHelp());
    }

    public function testMethodHelpShouldBeMutable(): void
    {
        self::assertEquals('', $this->definition->getMethodHelp());
        $this->definition->setMethodHelp('foo bar');
        self::assertEquals('foo bar', $this->definition->getMethodHelp());
    }

    public function testNameShouldBeNullByDefault(): void
    {
        self::assertNull($this->definition->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        self::assertNull($this->definition->getName());
        $this->definition->setName('foo.bar');
        self::assertEquals('foo.bar', $this->definition->getName());
    }

    public function testObjectShouldBeNullByDefault(): void
    {
        self::assertNull($this->definition->getObject());
    }

    public function testObjectShouldBeMutable(): void
    {
        self::assertNull($this->definition->getObject());
        $object = new stdClass();
        $this->definition->setObject($object);
        self::assertEquals($object, $this->definition->getObject());
    }

    public function testInvokeArgumentsShouldBeEmptyArrayByDefault(): void
    {
        $args = $this->definition->getInvokeArguments();
        self::assertIsArray($args);
        self::assertEmpty($args);
    }

    public function testInvokeArgumentsShouldBeMutable(): void
    {
        $this->testInvokeArgumentsShouldBeEmptyArrayByDefault();
        $args = ['foo', ['bar', 'baz'], new stdClass()];
        $this->definition->setInvokeArguments($args);
        self::assertSame($args, $this->definition->getInvokeArguments());
    }

    public function testPrototypesShouldBeEmptyArrayByDefault(): void
    {
        $prototypes = $this->definition->getPrototypes();
        self::assertIsArray($prototypes);
        self::assertEmpty($prototypes);
    }

    public function testDefinitionShouldAllowAddingSinglePrototypes(): void
    {
        $this->testPrototypesShouldBeEmptyArrayByDefault();
        $prototype1 = new Prototype();
        $this->definition->addPrototype($prototype1);
        $test = $this->definition->getPrototypes();
        self::assertSame($prototype1, $test[0]);

        $prototype2 = new Prototype();
        $this->definition->addPrototype($prototype2);
        $test = $this->definition->getPrototypes();
        self::assertSame($prototype1, $test[0]);
        self::assertSame($prototype2, $test[1]);
    }

    public function testDefinitionShouldAllowAddingMultiplePrototypes(): void
    {
        $prototype1 = new Prototype();
        $prototype2 = new Prototype();
        $prototypes = [$prototype1, $prototype2];
        $this->definition->addPrototypes($prototypes);
        self::assertSame($prototypes, $this->definition->getPrototypes());
    }

    public function testSetPrototypesShouldOverwriteExistingPrototypes(): void
    {
        $this->testDefinitionShouldAllowAddingMultiplePrototypes();

        $prototype1 = new Prototype();
        $prototype2 = new Prototype();
        $prototypes = [$prototype1, $prototype2];
        self::assertNotSame($prototypes, $this->definition->getPrototypes());
        $this->definition->setPrototypes($prototypes);
        self::assertSame($prototypes, $this->definition->getPrototypes());
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
        self::assertEquals($name, $test['name']);
        self::assertEquals($callback, $test['callback']);
        self::assertEquals($prototypes, $test['prototypes']);
        self::assertEquals($methodHelp, $test['methodHelp']);
        self::assertEquals($object, $test['object']);
        self::assertEquals($invokeArgs, $test['invokeArguments']);
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
        $definition = new Definition($options);
        $test       = $definition->toArray();
        self::assertEquals($options['name'], $test['name']);
        self::assertEquals($options['callback'], $test['callback']);
        self::assertEquals($options['prototypes'], $test['prototypes']);
        self::assertEquals($options['methodHelp'], $test['methodHelp']);
        self::assertEquals($options['object'], $test['object']);
        self::assertEquals($options['invokeArguments'], $test['invokeArguments']);
    }
}
