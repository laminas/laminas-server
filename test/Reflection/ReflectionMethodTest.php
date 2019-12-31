<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;

/**
 * Test case for \Laminas\Server\Reflection\ReflectionMethod
 *
 * @group      Laminas_Server
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase
{
    protected $_classRaw;
    protected $_class;
    protected $_method;

    protected function setUp()
    {
        $this->_classRaw = new \ReflectionClass('\Laminas\Server\Reflection');
        $this->_method   = $this->_classRaw->getMethod('reflectClass');
        $this->_class    = new Reflection\ReflectionClass($this->_classRaw);
    }

    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - class:
     * - r:
     * - namespace: Optional;
     * - argv: Optional; has default;
     *
     * Returns: void
     */
    public function test__construct()
    {
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);
        $this->assertTrue($r instanceof Reflection\ReflectionMethod);
        $this->assertTrue($r instanceof Reflection\AbstractFunction);

        $r = new Reflection\ReflectionMethod($this->_class, $this->_method, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * getDeclaringClass() test
     *
     * Call as method call
     *
     * Returns: \Laminas\Server\Reflection\ReflectionClass
     */
    public function testGetDeclaringClass()
    {
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);

        $class = $r->getDeclaringClass();

        $this->assertTrue($class instanceof Reflection\ReflectionClass);
        $this->assertTrue($this->_class === $class);
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
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertTrue($u instanceof Reflection\ReflectionMethod);
        $this->assertTrue($u instanceof Reflection\AbstractFunction);
        $this->assertEquals($r->getName(), $u->getName());
        $this->assertEquals($r->getDeclaringClass()->getName(), $u->getDeclaringClass()->getName());
    }
}
