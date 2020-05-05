<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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
    public function testReflectClass(): void
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class);
        $this->assertInstanceOf(ReflectionClass::class, $reflection);

        $reflection = Reflection::reflectClass(new TestAsset\ReflectionTestClass());
        $this->assertInstanceOf(ReflectionClass::class, $reflection);
    }

    public function testReflectClassThrowsExceptionOnInvalidClass(): void
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argv argument passed to reflectClass');
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectClassThrowsExceptionOnInvalidParameter(): void
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class or object passed to attachClass');
        $reflection = Reflection::reflectClass(false);
    }

    /**
     * reflectClass() test; test namespaces
     */
    public function testReflectClass2(): void
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class, false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    /**
     * reflectFunction() test
     */
    public function testReflectFunction(): void
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction');
        $this->assertInstanceOf(ReflectionFunction::class, $reflection);
    }

    public function testReflectFunctionThrowsExceptionOnInvalidFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        $reflection = Reflection::reflectFunction(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectFunctionThrowsExceptionOnInvalidParam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        $reflection = Reflection::reflectFunction(false);
    }

    /**
     * reflectFunction() test; test namespaces
     */
    public function testReflectFunction2(): void
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction', false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }
}
