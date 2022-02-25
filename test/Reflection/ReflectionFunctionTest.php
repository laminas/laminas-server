<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

/**
 * @group      Laminas_Server
 */
class ReflectionFunctionTest extends TestCase
{
    public function testConstructor(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);
        $params = $r->getParameters();

        $r = new Reflection\ReflectionFunction($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());

        $argv = ['string1', 'string2'];
        $r = new Reflection\ReflectionFunction($function, 'namespace', $argv);
        $this->assertIsArray($r->getInvokeArguments());
        $this->assertEquals($argv, $r->getInvokeArguments());

        $prototypes = $r->getPrototypes();
        $this->assertNotEmpty($prototypes);
    }

    public function testPropertyOverloading(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);

        $r->system = true;
        $this->assertTrue($r->system);
    }


    public function testNamespace(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
        $r->setNamespace('framework');
        $this->assertEquals('framework', $r->getNamespace());
    }

    public function testDescription(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);
        $this->assertStringContainsString('function for reflection', $r->getDescription());
        $r->setDescription('Testing setting descriptions');
        $this->assertEquals('Testing setting descriptions', $r->getDescription());
    }

    public function testGetPrototypes(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        $this->assertCount(8, $prototypes);

        foreach ($prototypes as $p) {
            $this->assertTrue(in_array($p->getReturnType(), ['null', 'array'], true));
        }
    }

    public function testGetPrototypes2(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function2');
        $r = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        $this->assertNotEmpty($prototypes);
        $this->assertCount(1, $prototypes);

        foreach ($prototypes as $p) {
            $this->assertSame('void', $p->getReturnType());
        }
    }


    public function testGetInvokeArguments(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);
        $args = $r->getInvokeArguments();
        $this->assertCount(0, $args);

        $argv = ['string1', 'string2'];
        $r = new Reflection\ReflectionFunction($function, null, $argv);
        $args = $r->getInvokeArguments();
        $this->assertEquals($argv, $args);
    }

    public function testClassWakeup(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);
        $s = serialize($r);
        $u = unserialize($s);
        $this->assertInstanceOf(\Laminas\Server\Reflection\ReflectionFunction::class, $u);
        $this->assertEquals('', $u->getNamespace());
    }

    public function testMultipleWhitespaceBetweenDoctagsAndTypes(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function3');
        $r = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        $this->assertNotEmpty($prototypes);
        $this->assertCount(1, $prototypes);

        $proto = $prototypes[0];
        $params = $proto->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('string', $params[0]->getType());
    }

    /**
     * @group Laminas-6996
     *
     * @return void
     */
    public function testParameterReflectionShouldReturnTypeAndVarnameAndDescription(): void
    {
        $function = new ReflectionFunction('LaminasTest\Server\Reflection\TestAsset\function1');
        $r = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        $prototype  = $prototypes[0];
        $params = $prototype->getParameters();
        $param  = $params[0];
        $this->assertStringContainsString('Some description', $param->getDescription(), var_export($param, 1));
    }
}
