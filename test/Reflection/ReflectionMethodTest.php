<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Node;
use Laminas\Server\Reflection\ReflectionParameter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

use function serialize;
use function unserialize;

class ReflectionMethodTest extends TestCase
{
    /** @var ReflectionClass */
    protected $classRaw;

    /** @var Reflection\ReflectionClass */
    protected $class;

    /** @var ReflectionMethod */
    protected $method;

    protected function setUp(): void
    {
        $this->classRaw = new ReflectionClass(Reflection::class);
        $this->method   = $this->classRaw->getMethod('reflectClass');
        $this->class    = new Reflection\ReflectionClass($this->classRaw);
    }

    public function testConstructor(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);
        $this->assertEquals('', $r->getNamespace());

        $r = new Reflection\ReflectionMethod($this->class, $this->method, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    public function testGetDeclaringClass(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);

        $class = $r->getDeclaringClass();

        $this->assertEquals($this->class, $class);
    }

    public function testClassWakeup(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertInstanceOf(Reflection\ReflectionMethod::class, $u);
        $this->assertEquals($r->getName(), $u->getName());
        $this->assertEquals($r->getDeclaringClass()->getName(), $u->getDeclaringClass()->getName());
    }

    public function testMethodDocBlockFromInterface(): void
    {
        $reflectionClass  = new ReflectionClass(TestAsset\ReflectionMethodTestInstance::class);
        $reflectionMethod = $reflectionClass->getMethod('testMethod');

        $laminasReflectionMethod = new Reflection\ReflectionMethod(
            new Reflection\ReflectionClass($reflectionClass),
            $reflectionMethod
        );
        [$prototype]             = $laminasReflectionMethod->getPrototypes();
        [$first, $second]        = $prototype->getParameters();

        self::assertInstanceOf(ReflectionParameter::class, $first);
        self::assertInstanceOf(ReflectionParameter::class, $second);
        self::assertEquals('ReflectionMethodTest', $first->getType());
        self::assertEquals('array', $second->getType());
    }

    public function testMethodDocBlockFromParent(): void
    {
        $reflectionClass  = new ReflectionClass(TestAsset\ReflectionMethodNode::class);
        $reflectionMethod = $reflectionClass->getMethod('setParent');

        $laminasReflectionMethod = new Reflection\ReflectionMethod(
            new Reflection\ReflectionClass($reflectionClass),
            $reflectionMethod
        );
        $prototypes              = $laminasReflectionMethod->getPrototypes();
        [$first, $second]        = $prototypes[1]->getParameters();

        self::assertEquals('\\' . Node::class, $first->getType());
        self::assertEquals('bool', $second->getType());
    }
}
