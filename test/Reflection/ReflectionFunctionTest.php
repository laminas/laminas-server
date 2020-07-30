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
use Laminas\Server\Reflection\Prototype;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

use function serialize;
use function unserialize;
use function var_export;

class ReflectionFunctionTest extends TestCase
{
    public function testConstructor(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);
        self::assertInstanceOf(Reflection\ReflectionFunction::class, $r);
        self::assertInstanceOf(AbstractFunction::class, $r);
        $params = $r->getParameters();

        $r = new Reflection\ReflectionFunction($function, 'namespace');
        self::assertEquals('namespace', $r->getNamespace());

        $argv = ['string1', 'string2'];
        $r    = new Reflection\ReflectionFunction($function, 'namespace', $argv);
        self::assertIsArray($r->getInvokeArguments());
        self::assertEquals($argv, $r->getInvokeArguments());

        $prototypes = $r->getPrototypes();
        self::assertIsArray($prototypes);
        self::assertNotEmpty($prototypes);
    }

    public function testPropertyOverloading(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);

        $r->system = true;
        self::assertTrue($r->system);
    }

    public function testNamespace(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function, 'namespace');
        self::assertEquals('namespace', $r->getNamespace());
        $r->setNamespace('framework');
        self::assertEquals('framework', $r->getNamespace());
    }

    public function testSetNamespaceSetsEmptyStringToNull(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function, 'namespace');
        $r->setNamespace('');
        self::assertNull($r->getNamespace());
    }

    public function testSetNamespaceThrowsInvalidArgumentException(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function, 'namespace');
        $this->expectException(Reflection\Exception\InvalidArgumentException::class);
        $r->setNamespace('äöü');
    }

    public function testDescription(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);
        self::assertStringContainsString('function for reflection', $r->getDescription());
        $r->setDescription('Testing setting descriptions');
        self::assertEquals('Testing setting descriptions', $r->getDescription());
    }

    public function testGetPrototypes(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        self::assertIsArray($prototypes);
        self::assertCount(4, $prototypes);

        foreach ($prototypes as $p) {
            self::assertInstanceOf(Prototype::class, $p);
        }
    }

    public function testGetPrototypes2(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function2');
        $r        = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        self::assertIsArray($prototypes);
        self::assertNotEmpty($prototypes);
        self::assertCount(1, $prototypes);

        foreach ($prototypes as $p) {
            self::assertInstanceOf(Prototype::class, $p);
        }
    }

    public function testGetInvokeArguments(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);
        $args     = $r->getInvokeArguments();
        self::assertIsArray($args);
        self::assertCount(0, $args);

        $argv = ['string1', 'string2'];
        $r    = new Reflection\ReflectionFunction($function, null, $argv);
        $args = $r->getInvokeArguments();
        self::assertEquals($argv, $args);
    }

    public function testClassWakeup(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);
        $s        = serialize($r);
        $u        = unserialize($s);
        self::assertInstanceOf(Reflection\ReflectionFunction::class, $u);
        self::assertEquals('', $u->getNamespace());
    }

    public function testMultipleWhitespaceBetweenDoctagsAndTypes(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function3');
        $r        = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        self::assertIsArray($prototypes);
        self::assertNotEmpty($prototypes);
        self::assertCount(1, $prototypes);

        $proto  = $prototypes[0];
        $params = $proto->getParameters();
        self::assertIsArray($params);
        self::assertCount(1, $params);
        self::assertEquals('string', $params[0]->getType());
    }

    public function testParameterReflectionShouldReturnTypeAndVarnameAndDescription(): void
    {
        $function = new ReflectionFunction('\LaminasTest\Server\Reflection\function1');
        $r        = new Reflection\ReflectionFunction($function);

        $prototypes = $r->getPrototypes();
        $prototype  = $prototypes[0];
        $params     = $prototype->getParameters();
        $param      = $params[0];
        self::assertStringContainsString('Some description', $param->getDescription(), var_export($param, true));
    }
}

// phpcs:disable
/**
 * \LaminasTest\Server\Reflection\function1
 *
 * Test function for reflection unit tests
 *
 * @param string       $var1 Some description
 * @param string|array $var2
 * @param array $var3
 * @return null|array
 */
function function1(string $var1, $var2, array $var3): ?array
{
}
// phpcs:enable

/**
 * \LaminasTest\Server\Reflection\function2
 *
 * Test function for reflection unit tests; test what happens when no return
 * value or params specified in docblock.
 */
function function2(): void
{
}

// phpcs:disable
/**
 * \LaminasTest\Server\Reflection\function3
 *
 * @param  string $var1
 * @return void
 */
// phpcs:enable
function function3(string $var1): void
{
}
