<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Exception\InvalidArgumentException;
use Laminas\Server\Reflection\Prototype;
use Laminas\Server\Reflection\ReflectionParameter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PrototypeTest extends TestCase
{
    /** @var Prototype */
    protected $r;

    /** @var ReflectionParameter[] */
    protected $parametersRaw;

    /** @var ReflectionParameter[] */
    protected $parameters;

    protected function setUp(): void
    {
        $class               = new ReflectionClass(Reflection::class);
        $method              = $class->getMethod('reflectClass');
        $parameters          = $method->getParameters();
        $this->parametersRaw = $parameters;

        $fParameters = [];
        foreach ($parameters as $p) {
            $fParameters[] = new Reflection\ReflectionParameter($p);
        }
        $this->parameters = $fParameters;

        $this->r = new Prototype(new Reflection\ReflectionReturnValue('void', 'No return'));
    }

    protected function tearDown(): void
    {
        unset($this->r);
        unset($this->parameters);
        unset($this->parametersRaw);
    }

    public function testConstructWorks(): void
    {
        $this->assertInstanceOf(Prototype::class, $this->r);
    }

    public function testConstructionThrowsExceptionOnInvalidParam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more params are invalid');
        $r1 = new Prototype($this->r->getReturnValue(), $this->parametersRaw);
    }

    public function testGetReturnType(): void
    {
        $this->assertEquals('void', $this->r->getReturnType());
    }

    public function testGetParameters(): void
    {
        $r = new Prototype($this->r->getReturnValue(), $this->parameters);
        $p = $r->getParameters();

        $this->assertIsArray($p);
        foreach ($p as $parameter) {
            $this->assertInstanceOf(ReflectionParameter::class, $parameter);
        }

        $this->assertEquals($this->parameters, $p);
    }
}
