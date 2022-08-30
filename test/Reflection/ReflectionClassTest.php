<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
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
        $this->assertInstanceOf(ReflectionClass::class, $r);
        $this->assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        foreach ($methods as $m) {
            $this->assertInstanceOf(ReflectionMethod::class, $m);
        }

        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class), 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    public function testMethodOverloading(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $this->assertIsString($r->getName());
        $this->assertEquals(Reflection::class, $r->getName());
    }

    public function testGetSet(): void
    {
        $r         = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $r->system = true;
        $this->assertTrue($r->system);
    }

    public function testGetMethods(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));

        $methods = $r->getMethods();
        foreach ($methods as $m) {
            $this->assertInstanceOf(ReflectionMethod::class, $m);
        }
    }

    public function testGetNamespace(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $this->assertEquals('', $r->getNamespace());
        $r->setNamespace('namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    public function testSetNamespaceSetsEmptyStringToNull(): void
    {
        $r = new Reflection\ReflectionClass(new PhpReflectionClass(Reflection::class));
        $r->setNamespace('');
        $this->assertNull($r->getNamespace());
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

        $this->assertInstanceOf(ReflectionClass::class, $u);
        $this->assertEquals('', $u->getNamespace());
        $this->assertEquals($r->getName(), $u->getName());
        $rMethods = $r->getMethods();
        $uMethods = $r->getMethods();

        $this->assertCount(count($rMethods), $uMethods);
    }
}
