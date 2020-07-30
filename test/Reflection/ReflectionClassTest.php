<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\ReflectionClass;
use Laminas\Server\Reflection\ReflectionMethod;
use PHPUnit\Framework\TestCase;
use ReflectionClass as PhpReflectionClass;

use function count;
use function serialize;
use function unserialize;

class ReflectionClassTest extends TestCase
{
    public function testConstructor(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        self::assertInstanceOf(ReflectionClass::class, $r);
        self::assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        self::assertIsArray($methods);
        foreach ($methods as $m) {
            self::assertInstanceOf(ReflectionMethod::class, $m);
        }

        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class), 'namespace');
        self::assertEquals('namespace', $r->getNamespace());
    }

    public function testMethodOverloading(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        self::assertIsString($r->getName());
        self::assertEquals(Reflection::class, $r->getName());
    }

    public function testGetSet(): void
    {
        $r         = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $r->system = true;
        self::assertTrue($r->system);
    }

    public function testGetMethods(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));

        $methods = $r->getMethods();
        self::assertIsArray($methods);
        foreach ($methods as $m) {
            self::assertInstanceOf(ReflectionMethod::class, $m);
        }
    }

    public function testGetNamespace(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        self::assertEquals('', $r->getNamespace());
        $r->setNamespace('namespace');
        self::assertEquals('namespace', $r->getNamespace());
    }

    public function testSetNamespaceSetsEmptyStringToNull(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $r->setNamespace('');
        self::assertNull($r->getNamespace());
    }

    public function testSetNamespaceThrowsInvalidArgumentException(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $r->setNamespace('äöü');
    }

    public function testClassWakeup(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $s = serialize($r);
        $u = unserialize($s);

        self::assertInstanceOf(ReflectionClass::class, $u);
        self::assertEquals('', $u->getNamespace());
        self::assertEquals($r->getName(), $u->getName());
        $rMethods = $r->getMethods();
        $uMethods = $r->getMethods();

        self::assertCount(count($rMethods), $uMethods);
    }
}
