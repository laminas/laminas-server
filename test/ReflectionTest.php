<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use TypeError;

#[Group('Laminas_server')]
final class ReflectionTest extends TestCase
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
        $this->expectException(TypeError::class);
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectClass(false);
    }

    /**
     * reflectClass() test; test namespaces
     */
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
        $this->expectException(TypeError::class);
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectFunction(false);
    }

    /**
     * reflectFunction() test; test namespaces
     */
    public function testReflectFunction2(): void
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\Reflection\TestAsset\function1', null, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    public function testReflectFunctionAllowsNullArgv(): void
    {
        $r = Reflection::reflectFunction('LaminasTest\Server\Reflection\TestAsset\function1', null);
        $this->assertSame([], $r->getInvokeArguments());
    }
}
