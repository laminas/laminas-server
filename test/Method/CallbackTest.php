<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Method;

use Laminas\Server\Exception\InvalidArgumentException;
use Laminas\Server\Method;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    /** @var Method\Callback */
    private $callback;

    protected function setUp(): void
    {
        $this->callback = new Method\Callback();
    }

    public function testClassShouldBeNullByDefault(): void
    {
        self::assertNull($this->callback->getClass());
    }

    public function testClassShouldBeMutable(): void
    {
        self::assertNull($this->callback->getClass());
        $this->callback->setClass('Foo');
        self::assertEquals('Foo', $this->callback->getClass());
    }

    public function testMethodShouldBeNullByDefault(): void
    {
        self::assertNull($this->callback->getMethod());
    }

    public function testMethodShouldBeMutable(): void
    {
        self::assertNull($this->callback->getMethod());
        $this->callback->setMethod('foo');
        self::assertEquals('foo', $this->callback->getMethod());
    }

    public function testFunctionShouldBeNullByDefault(): void
    {
        self::assertNull($this->callback->getFunction());
    }

    public function testFunctionShouldBeMutable(): void
    {
        self::assertNull($this->callback->getFunction());
        $this->callback->setFunction('foo');
        self::assertEquals('foo', $this->callback->getFunction());
    }

    public function testFunctionMayBeCallable(): void
    {
        $callable = function () {
            return true;
        };
        $this->callback->setFunction($callable);
        self::assertEquals($callable, $this->callback->getFunction());
    }

    public function testTypeShouldBeAnEmptyStringByDefault(): void
    {
        self::assertNull($this->callback->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        self::assertNull($this->callback->getType());
        $this->callback->setType('instance');
        self::assertEquals('instance', $this->callback->getType());
    }

    public function testSettingTypeShouldThrowExceptionWhenInvalidTypeProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid method callback type');
        $this->callback->setType('bogus');
    }

    public function testCallbackShouldSerializeToArray(): void
    {
        $this->callback->setClass('Foo')
                       ->setMethod('bar')
                       ->setType('instance');
        $test = $this->callback->toArray();
        self::assertIsArray($test);
        self::assertEquals('Foo', $test['class']);
        self::assertEquals('bar', $test['method']);
        self::assertEquals('instance', $test['type']);
    }

    public function testConstructorShouldSetStateFromOptions(): void
    {
        $options  = [
            'type'   => 'static',
            'class'  => 'Foo',
            'method' => 'bar',
        ];
        $callback = new Method\Callback($options);
        $test     = $callback->toArray();
        self::assertSame($options, $test);
    }

    public function testSettingFunctionShouldSetTypeAsFunction(): void
    {
        self::assertNull($this->callback->getType());
        $this->callback->setFunction('foo');
        self::assertEquals('function', $this->callback->getType());
    }
}
