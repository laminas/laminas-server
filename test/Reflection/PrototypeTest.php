<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\Prototype;
use Laminas\Server\Reflection\ReflectionParameter;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionParameter as PhpReflectionParameter;

/**
 * Test case for \Laminas\Server\Reflection\Prototype
 */
#[Group('Laminas_Server')]
final class PrototypeTest extends TestCase
{
    protected Prototype $r;

    /**
     * @var PhpReflectionParameter[]
     * @psalm-var list<PhpReflectionParameter>
     */
    protected array $parametersRaw;

    /** @var ReflectionParameter[] */
    protected $parameters;

    /**
     * Setup environment
     */
    #[Override]
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

    /**
     * Teardown environment
     */
    #[Override]
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

    public function testGetReturnType(): void
    {
        $this->assertEquals('void', $this->r->getReturnType());
    }

    public function testGetParameters(): void
    {
        $r = new Prototype($this->r->getReturnValue(), $this->parameters);
        $p = $r->getParameters();

        foreach ($p as $parameter) {
            $this->assertInstanceOf(ReflectionParameter::class, $parameter);
        }

        $this->assertEquals($this->parameters, $p);
    }
}
