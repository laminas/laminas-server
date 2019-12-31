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
    protected $r;

    /**
     * Array of ReflectionParameters
     * @var array
     */
    protected $parametersRaw;

    /**
     * Array of \Laminas\Server\Reflection\Parameters
     * @var array
     */
    protected $parameters;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $class = new \ReflectionClass('\Laminas\Server\Reflection');
        $method = $class->getMethod('reflectClass');
        $parameters = $method->getParameters();
        $this->parametersRaw = $parameters;

        $fParameters = [];
        foreach ($parameters as $p) {
            $fParameters[] = new Reflection\ReflectionParameter($p);
        }
        $this->parameters = $fParameters;

        $this->r = new Reflection\Prototype(new Reflection\ReflectionReturnValue('void', 'No return'));
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->r);
        unset($this->parameters);
        unset($this->parametersRaw);
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
        $this->assertInstanceOf('Laminas\Server\Reflection\Prototype', $this->r);
    }

    public function testConstructionThrowsExceptionOnInvalidParam()
    {
        $this->setExpectedException(
            'Laminas\Server\Reflection\Exception\InvalidArgumentException',
            'One or more params are invalid'
        );
        $r1 = new Reflection\Prototype($this->r->getReturnValue(), $this->parametersRaw);
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
        $this->assertEquals('void', $this->r->getReturnType());
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
        $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionReturnValue', $this->r->getReturnValue());
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
        $r = new Reflection\Prototype($this->r->getReturnValue(), $this->parameters);
        $p = $r->getParameters();

        $this->assertInternalType('array', $p);
        foreach ($p as $parameter) {
            $this->assertInstanceOf('Laminas\Server\Reflection\ReflectionParameter', $parameter);
        }

        $this->assertEquals($this->parameters, $p);
    }
}
