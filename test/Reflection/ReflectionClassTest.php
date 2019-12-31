<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;

/**
 * Test case for \Laminas\Server\Reflection\ClassReflection
 *
 * @group      Laminas_Server
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
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
     */
    public function test__construct()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));
        $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionClass', $r);
        $this->assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        $this->assertInternalType('array', $methods);
        foreach ($methods as $m) {
            $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionMethod', $m);
        }

        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'), 'namespace');
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
     */
    public function test__call()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));
        $this->assertInternalType('string', $r->getName());
        $this->assertEquals('Laminas\Server\Reflection', $r->getName());
    }

    /**
     * test __get/set
     */
    public function testGetSet()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));
        $r->system = true;
        $this->assertTrue($r->system);
    }

    /**
     * getMethods() test
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testGetMethods()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));

        $methods = $r->getMethods();
        $this->assertInternalType('array', $methods);
        foreach ($methods as $m) {
            $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionMethod', $m);
        }
    }

    /**
     * namespace test
     */
    public function testGetNamespace()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));
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
     */
    public function test__wakeup()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Laminas\Server\Reflection'));
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionClass', $u);
        $this->assertEquals('', $u->getNamespace());
        $this->assertEquals($r->getName(), $u->getName());
        $rMethods = $r->getMethods();
        $uMethods = $r->getMethods();

        $this->assertEquals(count($rMethods), count($uMethods));
    }
}
