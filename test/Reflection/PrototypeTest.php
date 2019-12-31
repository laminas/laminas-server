<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;

/**
 * Test case for \Laminas\Server\Reflection\Prototype
 *
 * @group      Laminas_Server
 */
class PrototypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * \Laminas\Server\Reflection\Prototype object
     * @var \Laminas\Server\Reflection\Prototype
     */
    protected $_r;

    /**
     * Array of ReflectionParameters
     * @var array
     */
    protected $_parametersRaw;

    /**
     * Array of \Laminas\Server\Reflection\Parameters
     * @var array
     */
    protected $_parameters;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $class = new \ReflectionClass('\Laminas\Server\Reflection');
        $method = $class->getMethod('reflectClass');
        $parameters = $method->getParameters();
        $this->_parametersRaw = $parameters;

        $fParameters = array();
        foreach ($parameters as $p) {
            $fParameters[] = new Reflection\ReflectionParameter($p);
        }
        $this->_parameters = $fParameters;

        $this->_r = new Reflection\Prototype(new Reflection\ReflectionReturnValue('void', 'No return'));
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_r);
        unset($this->_parameters);
        unset($this->_parametersRaw);
    }

    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - return:
     * - params: Optional;
     *
     * Returns: void
     */
    public function testConstructWorks()
    {
        $this->assertTrue($this->_r instanceof Reflection\Prototype);
    }

    public function testConstructionThrowsExceptionOnInvalidParam()
    {
        $this->setExpectedException('Laminas\Server\Reflection\Exception\InvalidArgumentException', 'One or more params are invalid');
        $r1 = new Reflection\Prototype($this->_r->getReturnValue(), $this->_parametersRaw);
    }

    /**
     * getReturnType() test
     *
     * Call as method call
     *
     * Returns: string
     */
    public function testGetReturnType()
    {
        $this->assertEquals('void', $this->_r->getReturnType());
    }

    /**
     * getReturnValue() test
     *
     * Call as method call
     *
     * Returns: \Laminas\Server\Reflection\ReflectionReturnValue
     */
    public function testGetReturnValue()
    {
        $this->assertTrue($this->_r->getReturnValue() instanceof Reflection\ReflectionReturnValue);
    }

    /**
     * getParameters() test
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testGetParameters()
    {
        $r = new Reflection\Prototype($this->_r->getReturnValue(), $this->_parameters);
        $p = $r->getParameters();

        $this->assertTrue(is_array($p));
        foreach ($p as $parameter) {
            $this->assertTrue($parameter instanceof Reflection\ReflectionParameter);
        }

        $this->assertTrue($p === $this->_parameters);
    }
}
