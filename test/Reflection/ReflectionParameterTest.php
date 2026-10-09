<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter as PhpReflectionParameter;

/**
 * Test case for \Laminas\Server\Reflection\ReflectionParameter
 */
#[Group('Laminas_Server')]
final class ReflectionParameterTest extends TestCase
{
    protected function getParameter(): PhpReflectionParameter
    {
        $method     = new ReflectionMethod(ReflectionParameter::class, 'setType');
        $parameters = $method->getParameters();
        return $parameters[0];
    }

    public function testConstructor(): void
    {
        $parameter = $this->getParameter();

        $reflection = new ReflectionParameter($parameter);
        $this->assertInstanceOf(ReflectionParameter::class, $reflection);
    }

    public function testMethodOverloading(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());

        // just test a few call proxies...
        $this->assertIsBool($r->allowsNull());
        $this->assertIsBool($r->isOptional());
    }

    public function testGetSetType(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        $this->assertEquals('mixed', $r->getType());

        $r->setType('string');
        $this->assertEquals('string', $r->getType());
    }

    public function testGetDescription(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        $this->assertEquals('', $r->getDescription());

        $r->setDescription('parameter description');
        $this->assertEquals('parameter description', $r->getDescription());
    }

    public function testSetPosition(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        $this->assertEquals(null, $r->getPosition());

        $r->setPosition(3);
        $this->assertEquals(3, $r->getPosition());
    }
}
