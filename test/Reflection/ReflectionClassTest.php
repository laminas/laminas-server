<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\ReflectionClass;
use Laminas\Server\Reflection\ReflectionMethod;
use PHPUnit\Framework\TestCase;

/**
 * Test case for \Laminas\Server\Reflection\ClassReflection
 *
 * @group      Laminas_Server
 */
class ReflectionClassTest extends TestCase
{
    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - reflection:
     * - namespace: Optional;
     * - argv: Optional; has default;
     *
     * Returns: void
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));
        $this->assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        foreach ($methods as $m) {
            $this->assertInstanceOf(ReflectionMethod::class, $m);
        }

        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class), 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * __call() test
     *
     * Call as method call
     *
     * Expects:
     * - method:
     * - args:
     *
     * Returns: mixed
     *
     * @return void
     */
    public function testMethodOverloading(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));
        $this->assertIsString($r->getName());
        $this->assertEquals('Laminas\Server\Reflection', $r->getName());
    }

    /**
     * test __get/set
     *
     * @return void
     */
    public function testGetSet(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));
        $r->system = true;
        $this->assertTrue($r->system);
    }

    /**
     * getMethods() test
     *
     * Call as method call
     *
     * Returns: array
     *
     * @return void
     */
    public function testGetMethods(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));

        $methods = $r->getMethods();
        foreach ($methods as $m) {
            $this->assertInstanceOf(ReflectionMethod::class, $m);
        }
    }

    /**
     * namespace test
     *
     * @return void
     */
    public function testGetNamespace(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));
        $this->assertEquals('', $r->getNamespace());
        $r->setNamespace('namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * __wakeup() test
     *
     * Call as method call
     *
     * Returns: void
     *
     * @return void
     */
    public function testClassWakeup(): void
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass(Reflection::class));
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
