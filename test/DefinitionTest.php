<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server;

use Laminas\Server;
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
        $this->definition = new Server\Definition();
    }

    public function testMethodsShouldBeEmptyArrayByDefault(): void
    {
        $methods = $this->definition->getMethods();
        $this->assertEmpty($methods);
    }

    public function testDefinitionShouldAllowAddingSingleMethods(): void
    {
        $method = new Method\Definition(['name' => 'foo']);
        $this->definition->addMethod($method);
        $methods = $this->definition->getMethods();
        $this->assertCount(1, $methods);
        $this->assertSame($method, $methods['foo']);
        $this->assertSame($method, $this->definition->getMethod('foo'));
    }

    public function testConstructorNumericKeyWillBeReplacedByMethodName(): void
    {
        $method     = new Method\Definition(['name' => 'foo']);
        $definition = new Server\Definition(['100' => $method]);

        $this->assertCount(1, $definition);
        $this->assertSame($method, $definition->getMethod('foo'));
    }

    public function testAddMethodNumericKeyWillBeReplacedByMethodName(): void
    {
        $method     = new Method\Definition(['name' => 'foo']);
        $definition = new Server\Definition();
        $definition->addMethod($method, '100');

        $this->assertCount(1, $definition);
        $this->assertSame($method, $definition->getMethod('foo'));
    }

    public function testDefinitionShouldAllowAddingMultipleMethods(): void
    {
        $method1 = new Method\Definition(['name' => 'foo']);
        $method2 = new Method\Definition(['name' => 'bar']);
        $this->definition->addMethods([$method1, $method2]);
        $methods = $this->definition->getMethods();
        $this->assertCount(2, $methods);
        $this->assertSame($method1, $methods['foo']);
        $this->assertSame($method1, $this->definition->getMethod('foo'));
        $this->assertSame($method2, $methods['bar']);
        $this->assertSame($method2, $this->definition->getMethod('bar'));
    }

    public function testSetMethodsShouldOverwriteExistingMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $method1 = new Method\Definition(['name' => 'foo']);
        $method2 = new Method\Definition(['name' => 'bar']);
        $methods = [$method1, $method2];
        $this->assertNotEquals($methods, $this->definition->getMethods());
        $this->definition->setMethods($methods);
        $test = $this->definition->getMethods();
        $this->assertSame($methods, array_values($test));
    }

    public function testHasMethodShouldReturnFalseWhenMethodNotRegisteredWithDefinition(): void
    {
        $this->assertFalse($this->definition->hasMethod('foo'));
    }

    public function testHasMethodShouldReturnTrueWhenMethodRegisteredWithDefinition(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->assertTrue($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowRemovingIndividualMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->assertTrue($this->definition->hasMethod('foo'));
        $this->definition->removeMethod('foo');
        $this->assertFalse($this->definition->hasMethod('foo'));
    }

    public function testDefinitionShouldAllowClearingAllMethods(): void
    {
        $this->testDefinitionShouldAllowAddingMultipleMethods();
        $this->definition->clearMethods();
        $test = $this->definition->getMethods();
        $this->assertEmpty($test);
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
        $definition = new Server\Definition();
        $definition->addMethod($method);
        $test = $definition->toArray();
        $this->assertCount(1, $test);
        $test = array_shift($test);
        $this->assertEquals($method['name'], $test['name']);
        $this->assertEquals($method['methodHelp'], $test['methodHelp']);
        $this->assertEquals($method['invokeArguments'], $test['invokeArguments']);
        $this->assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
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
        $definition = new Server\Definition($options);
        $test       = $definition->toArray();
        $this->assertCount(1, $test);
        $test = array_shift($test);
        $this->assertEquals($method['name'], $test['name']);
        $this->assertEquals($method['methodHelp'], $test['methodHelp']);
        $this->assertEquals($method['invokeArguments'], $test['invokeArguments']);
        $this->assertEquals($method['prototypes'][0]['returnType'], $test['prototypes'][0]['returnType']);
    }
}
