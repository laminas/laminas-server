<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Exception\InvalidArgumentException;
use Laminas\Server\Reflection\ReflectionClass;
use Laminas\Server\Reflection\ReflectionFunction;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Server
 */
class ReflectionTest extends TestCase
{
    /**
     * reflectClass() test
     */
    public function testReflectClass()
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class);
        $this->assertInstanceOf(ReflectionClass::class, $reflection);

        $reflection = Reflection::reflectClass(new TestAsset\ReflectionTestClass());
        $this->assertInstanceOf(ReflectionClass::class, $reflection);
    }

    public function testReflectClassThrowsExceptionOnInvalidClass()
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argv argument passed to reflectClass');
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectClassThrowsExceptionOnInvalidParameter()
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class or object passed to attachClass');
        $reflection = Reflection::reflectClass(false);
    }

    /**
     * reflectClass() test; test namespaces
     */
    public function testReflectClass2()
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class, false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    /**
     * reflectFunction() test
     */
    public function testReflectFunction()
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction');
        $this->assertInstanceOf(ReflectionFunction::class, $reflection);
    }

    public function testReflectFunctionThrowsExceptionOnInvalidFunction()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        $reflection = Reflection::reflectFunction(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectFunctionThrowsExceptionOnInvalidParam()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        $reflection = Reflection::reflectFunction(false);
    }

    /**
     * reflectFunction() test; test namespaces
     */
    public function testReflectFunction2()
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction', false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }
}
