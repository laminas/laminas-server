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
use PHPUnit\Framework\TestCase;

class ReflectionTest extends TestCase
{
    public function testReflectClass(): void
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class);
        $this->assertSame(TestAsset\ReflectionTestClass::class, $reflection->getName());

        $reflection = Reflection::reflectClass(new TestAsset\ReflectionTestClass());
        $this->assertSame(TestAsset\ReflectionTestClass::class, $reflection->getName());
    }

    public function testReflectClassThrowsExceptionOnInvalidParameter(): void
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class or object passed to attachClass');
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectClass(false);
    }

    public function testReflectClass2(): void
    {
        $reflection = Reflection::reflectClass(TestAsset\ReflectionTestClass::class, [], 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    public function testReflectFunctionThrowsExceptionOnInvalidFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        Reflection::reflectFunction(TestAsset\ReflectionTestClass::class, ['string']);
    }

    public function testReflectFunctionThrowsExceptionOnInvalidParam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectFunction(false);
    }

    public function testReflectFunction2(): void
    {
        /** @psalm-suppress UndefinedClass **/
        $reflection = Reflection::reflectFunction(TestAsset\reflectionTestFunction::class, null, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    public function testReflectFunctionAllowsNullArgv(): void
    {
        /** @psalm-suppress UndefinedClass **/
        $r = Reflection::reflectFunction(TestAsset\reflectionTestFunction::class, null);
        $this->assertSame([], $r->getInvokeArguments());
    }
}
