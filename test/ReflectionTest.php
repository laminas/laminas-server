<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server;

use Laminas\Server\Reflection;

/**
 * @category   Laminas
 * @package    Laminas_Server
 * @subpackage UnitTests
 * @group      Laminas_Server
 */
class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * reflectClass() test
     */
    public function testReflectClass()
    {
        $reflection = Reflection::reflectClass('LaminasTest\Server\ReflectionTestClass');
        $this->assertTrue($reflection instanceof Reflection\ReflectionClass);

        $reflection = Reflection::reflectClass(new ReflectionTestClass());
        $this->assertTrue($reflection instanceof Reflection\ReflectionClass);
    }

    public function testReflectClassThrowsExceptionOnInvalidClass()
    {
        $this->setExpectedException('Laminas\Server\Reflection\Exception\InvalidArgumentException', 'Invalid argv argument passed to reflectClass');
        $reflection = Reflection::reflectClass('LaminasTest\Server\ReflectionTestClass', 'string');
    }

    public function testReflectClassThrowsExceptionOnInvalidParameter()
    {
        $this->setExpectedException('Laminas\Server\Reflection\Exception\InvalidArgumentException', 'Invalid class or object passed to attachClass');
        $reflection = Reflection::reflectClass(false);
    }

    /**
     * reflectClass() test; test namespaces
     */
    public function testReflectClass2()
    {
        $reflection = Reflection::reflectClass('LaminasTest\Server\ReflectionTestClass', false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }

    /**
     * reflectFunction() test
     */
    public function testReflectFunction()
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\reflectionTestFunction');
        $this->assertTrue($reflection instanceof Reflection\ReflectionFunction);
    }

    public function testReflectFunctionThrowsExceptionOnInvalidFunction()
    {
        $this->setExpectedException('Laminas\Server\Reflection\Exception\InvalidArgumentException', 'Invalid function');
        $reflection = Reflection::reflectFunction('LaminasTest\Server\ReflectionTestClass', 'string');
    }

    public function testReflectFunctionThrowsExceptionOnInvalidParam()
    {
        $this->setExpectedException('Laminas\Server\Reflection\Exception\InvalidArgumentException', 'Invalid function');
        $reflection = Reflection::reflectFunction(false);
    }

    /**
     * reflectFunction() test; test namespaces
     */
    public function testReflectFunction2()
    {
        $reflection = Reflection::reflectFunction('LaminasTest\Server\reflectionTestFunction', false, 'zsr');
        $this->assertEquals('zsr', $reflection->getNamespace());
    }
}

/**
 * \LaminasTest\Server\reflectionTestClass
 *
 * Used to test reflectFunction generation of signatures
 *
 * @param boolean $arg1
 * @param string|array $arg2
 * @param string $arg3 Optional argument
 * @param string|struct|false $arg4 Optional argument
 * @return boolean|array
 */
function reflectionTestFunction($arg1, $arg2, $arg3 = 'string', $arg4 = 'array')
{
}

/**
 * \LaminasTest\Server\ReflectionTestClass -- test class reflection
 */
class ReflectionTestClass
{
    /**
     * Constructor
     *
     * This shouldn't be reflected
     *
     * @param mixed $arg
     */
    public function __construct($arg = null)
    {
    }

    /**
     * Public one
     *
     * @param string $arg1
     * @param array $arg2
     * @return string
     */
    public function one($arg1, $arg2 = null)
    {
    }

    /**
     * Protected _one
     *
     * Should not be reflected
     *
     * @param string $arg1
     * @param array $arg2
     * @return string
     */
    protected function _one($arg1, $arg2 = null)
    {
    }

    /**
     * Public two
     *
     * @param string $arg1
     * @param string $arg2
     * @return boolean|array
     */
    public static function two($arg1, $arg2)
    {
    }
}
