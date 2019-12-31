<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\ReflectionReturnValue;

/**
 * Test case for \Laminas\Server\Reflection\ReflectionReturnValue
 *
 * @group      Laminas_Server
 */
class ReflectionReturnValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - type: Optional; has default;
     * - description: Optional; has default;
     *
     * Returns: void
     */
    public function testConstructor()
    {
        $obj = new Reflection\ReflectionReturnValue();
        $this->assertInstanceOf(ReflectionReturnValue::class, $obj);
    }

    /**
     * getType() test
     *
     * Call as method call
     *
     * Returns: string
     */
    public function testGetType()
    {
        $obj = new Reflection\ReflectionReturnValue();
        $this->assertEquals('mixed', $obj->getType());

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    /**
     * setType() test
     *
     * Call as method call
     *
     * Expects:
     * - type:
     *
     * Returns: void
     */
    public function testSetType()
    {
        $obj = new Reflection\ReflectionReturnValue();

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    /**
     * getDescription() test
     *
     * Call as method call
     *
     * Returns: string
     */
    public function testGetDescription()
    {
        $obj = new Reflection\ReflectionReturnValue('string', 'Some description');
        $this->assertEquals('Some description', $obj->getDescription());

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }

    /**
     * setDescription() test
     *
     * Call as method call
     *
     * Expects:
     * - description:
     *
     * Returns: void
     */
    public function testSetDescription()
    {
        $obj = new Reflection\ReflectionReturnValue();

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }
}
