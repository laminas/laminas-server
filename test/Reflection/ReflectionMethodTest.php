<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\AbstractFunction;
use Laminas\Server\Reflection\Node;
use Laminas\Server\Reflection\ReflectionClass;
use Laminas\Server\Reflection\ReflectionMethod;
use PHPUnit\Framework\TestCase;
use ReflectionClass as PhpReflectionClass;
use ReflectionMethod as PhpReflectionMethod;

use function serialize;
use function unserialize;

class ReflectionMethodTest extends TestCase
{
    protected PhpReflectionClass $classRaw;
    protected ReflectionClass $class;
    protected PhpReflectionMethod $method;

    protected function setUp(): void
    {
        $this->classRaw = new PhpReflectionClass(Reflection::class);
        $this->method   = $this->classRaw->getMethod('reflectClass');
        $this->class    = new ReflectionClass($this->classRaw);
    }

    public function testConstructor(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);
        self::assertInstanceOf(ReflectionMethod::class, $r);
        self::assertInstanceOf(AbstractFunction::class, $r);

        $r = new Reflection\ReflectionMethod($this->class, $this->method, 'namespace');
        self::assertEquals('namespace', $r->getNamespace());
    }

    public function testGetDeclaringClass(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);

        $class = $r->getDeclaringClass();

        self::assertEquals($this->class, $class);
    }

    public function testClassWakeup(): void
    {
        $r = new Reflection\ReflectionMethod($this->class, $this->method);
        $s = serialize($r);
        $u = unserialize($s);

        self::assertInstanceOf(ReflectionMethod::class, $u);
        self::assertInstanceOf(AbstractFunction::class, $u);
        self::assertEquals($r->getName(), $u->getName());
        self::assertEquals($r->getDeclaringClass()->getName(), $u->getDeclaringClass()->getName());
    }

    public function testMethodDocBlockFromInterface(): void
    {
        $reflectionClass  = new PhpReflectionClass(TestAsset\ReflectionMethodTestInstance::class);
        $reflectionMethod = $reflectionClass->getMethod('testMethod');

        $laminasReflectionMethod = new Reflection\ReflectionMethod(
            new ReflectionClass($reflectionClass),
            $reflectionMethod
        );
        [$prototype]             = $laminasReflectionMethod->getPrototypes();
        [$first, $second]        = $prototype->getParameters();

        self::assertEquals('ReflectionMethodTest', $first->getType());
        self::assertEquals('array', $second->getType());
    }

    public function testMethodDocBlockFromParent(): void
    {
        $reflectionClass  = new PhpReflectionClass(TestAsset\ReflectionMethodNode::class);
        $reflectionMethod = $reflectionClass->getMethod('setParent');

        $laminasReflectionMethod = new Reflection\ReflectionMethod(
            new ReflectionClass($reflectionClass),
            $reflectionMethod
        );
        $prototypes              = $laminasReflectionMethod->getPrototypes();
        [$first, $second]        = $prototypes[1]->getParameters();

        self::assertEquals('\\' . Node::class, $first->getType());
        self::assertEquals('bool', $second->getType());
    }
}
