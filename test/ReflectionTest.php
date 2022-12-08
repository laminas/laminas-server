<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server;

use Laminas\Server\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;
use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Exception\InvalidArgumentException;
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
        $this->assertSame(TestAsset\ReflectionTestClass::class, $reflection->getName());
    }

    public function testReflectClassThrowsExceptionOnInvalidClass(): void
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argv argument passed to reflectClass');
        Reflection::reflectClass(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectClassThrowsExceptionOnInvalidParameter(): void
    {
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class or object passed to attachClass');
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectClass(false);
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
        $this->assertSame('LaminasTest\Server\TestAsset\reflectionTestFunction', $reflection->getName());
    }

    public function testReflectFunctionThrowsExceptionOnInvalidFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        Reflection::reflectFunction(TestAsset\ReflectionTestClass::class, 'string');
    }

    public function testReflectFunctionThrowsExceptionOnInvalidParam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid function');
        /** @psalm-suppress InvalidArgument */
        Reflection::reflectFunction(false);
    }

    /**
     * reflectFunction() test; test namespaces
     */
    public function testReflectFunction2(): void
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction', false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    /**
     * @psalm-return array<string, array{0: mixed}>
     */
    public function invalidArgvValues(): array
    {
        return [
            'true'          => [true],
            'zero'          => [0],
            'floating zero' => [0.0],
            'one'           => [1],
            'floating one'  => [1.1],
            'string'        => ['string'],
            'object'        => [(object) []],
        ];
    }

    /**
     * @dataProvider invalidArgvValues
     * @param mixed $invalidValue
     */
    public function testReflectFunctionThrowsExceptionForInvalidArgvValue($invalidValue): void
    {
        $this->expectException(ExceptionInvalidArgumentException::class);
        $this->expectExceptionMessage('argv argument');
        // Suppressing, as the test is intended to verify this
        /** @psalm-suppress MixedArgument */
        Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction', $invalidValue);
    }

    /**
     * @psalm-return array<string, array{0: null|bool}>
     */
    public function emptyArgvValues(): array
    {
        return [
            'false' => [false],
            'null'  => [null],
        ];
    }

    /**
     * @dataProvider emptyArgvValues
     */
    public function testReflectFunctionAllowsNullOrFalseArgv(?bool $argv): void
    {
        $r = Reflection::reflectFunction('LaminasTest\Server\TestAsset\reflectionTestFunction', $argv);
        $this->assertSame([], $r->getInvokeArguments());
    }
}
