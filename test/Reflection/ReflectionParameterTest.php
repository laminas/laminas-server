<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\ReflectionParameter;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter as PhpReflectionParameter;

class ReflectionParameterTest extends TestCase
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
        self::assertInstanceOf(ReflectionParameter::class, $reflection);
    }

    public function testMethodOverloading(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());

        // just test a few call proxies...
        self::assertIsBool($r->allowsNull());
        self::assertIsBool($r->isOptional());
    }

    public function testGetSetType(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        self::assertEquals('mixed', $r->getType());

        $r->setType('string');
        self::assertEquals('string', $r->getType());
    }

    public function testGetDescription(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        self::assertEquals('', $r->getDescription());

        $r->setDescription('parameter description');
        self::assertEquals('parameter description', $r->getDescription());
    }

    public function testSetPosition(): void
    {
        $r = new Reflection\ReflectionParameter($this->getParameter());
        self::assertEquals(null, $r->getPosition());

        $r->setPosition(3);
        self::assertEquals(3, $r->getPosition());
    }
}
