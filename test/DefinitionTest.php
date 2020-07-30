<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server;

use Laminas\Server\Definition;
use Laminas\Server\Method;
use PHPUnit\Framework\TestCase;

use function array_shift;
use function array_values;

class DefinitionTest extends TestCase
{
    private Definition $definition;

    protected function setUp(): void
    {
        $this->definition = new Definition();
    }

    public function testMethodsShouldBeEmptyArrayByDefault(): void
    {
        $methods = $this->definition->getMethods();
        self::assertIsArray($methods);
        self::assertEmpty($methods);
    }

    public function testDefinitionShouldAllowAddingSingleMethods(): void
    {
        $method = new Method\Definition(['name' => 'foo']);
        $this->definition->addMethod($method);
        $methods = $this->definition->getMethods();
        self::assertCount(1, $methods);
        self::assertSame($method, $methods['foo']);
        self::assertSame($method, $this->definition->getMethod('foo'));
    }

    public function testConstructorNumericKeyWillBeReplacedByMethodName(): void
    {
        $method     = new Method\Definition(['name' => 'foo']);
        $definition = new Definition(['100' => $method]);

        self::assertCount(1, $definition);
        self::assertSame($method, $definition->getMethod('foo'));
    }

    public function testAddMethodNumericKeyWillBeReplacedByMethodName(): void
    {
        $method     = new Method\Definition(['name' => 'foo']);
        $definition = new Definition();
        $definition->addMethod($method, '100');

        self::assertCount(1, $definition);
        self::assertSame($method, $definition->getMethod('foo'));
    }

    public function testDefinitionShouldAllowAddingMultipleMethods(): void
    {
        $method1 = new Method\Definition(['name' => 'foo']);
        $method2 = new Method\Definition(['name' => 'bar']);
        $this->definition->addMethods([$method1, $method2]);
        $methods = $this->definition->getMethods();
        self::assertCount(2, $methods);
        self::assertSame($method1, $methods['foo']);
        self::assertSame($method1, $this->definition->getMethod('foo'));
        self::assertSame($method2, $methods['bar']);
        self::assertSame($method2, $this->definition->getMethod('bar'));
    }

    public function testSetMethodsShouldOverwriteExistingMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $method1 = new Method\Definition(['name' => 'foo']);
        $method2 = new Method\Definition(['name' => 'bar']);
        $methods = [$method1, $method2];
        self::assertNotEquals($methods, $this->definition->getMethods());
        $this->definition->setMethods($methods);
        $test = $this->definition->getMethods();
        self::assertEquals(array_values($methods), array_values($test));
    }

    public function testHasMethodShouldReturnFalseWhenMethodNotRegisteredWithDefinition(): void
    {
        self::assertFalse($this->definition->hasMethod('foo'));
    }

    public function testHasMethodShouldReturnTrueWhenMethodRegisteredWithDefinition(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        self::assertTrue($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowRemovingIndividualMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        self::assertTrue($this->definition->hasMethod('foo'));
        $this->definition->removeMethod('foo');
        self::assertFalse($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowClearingAllMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->definition->clearMethods();
        $test = $this->definition->getMethods();
        self::assertEmpty($test);
    }

    public function testDefinitionShouldSerializeToArray(): void
    {
        $method     = [
            'name'            => 'foo.bar',
            'callback'        => [
                'type'     => 'function',
                'function' => 'bar',
            ],
            'prototypes'      => [
                [
                    'returnType' => 'string',
                    'parameters' => ['string'],
                ],
            ],
            'methodHelp'      => 'Foo Bar!',
            'invokeArguments' => ['foo'],
        ];
        $definition = new Definition();
        $definition->addMethod($method);
        $test = $definition->toArray();
        self::assertCount(1, $test);
        $test = array_shift($test);
        self::assertEquals($method['name'], $test['name']);
        self::assertEquals($method['methodHelp'], $test['methodHelp']);
        self::assertEquals($method['invokeArguments'], $test['invokeArguments']);
        self::assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }

    public function testPassingOptionsToConstructorShouldSetObjectState(): void
    {
        $method     = [
            'name'            => 'foo.bar',
            'callback'        => [
                'type'     => 'function',
                'function' => 'bar',
            ],
            'prototypes'      => [
                [
                    'returnType' => 'string',
                    'parameters' => ['string'],
                ],
            ],
            'methodHelp'      => 'Foo Bar!',
            'invokeArguments' => ['foo'],
        ];
        $options    = [$method];
        $definition = new Definition($options);
        $test       = $definition->toArray();
        self::assertCount(1, $test);
        $test = array_shift($test);
        self::assertEquals($method['name'], $test['name']);
        self::assertEquals($method['methodHelp'], $test['methodHelp']);
        self::assertEquals($method['invokeArguments'], $test['invokeArguments']);
        self::assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }
}
