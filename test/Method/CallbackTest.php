<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Method;

use Laminas\Server\Exception\InvalidArgumentException;
use Laminas\Server\Method;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Laminas\Server\Method\Callback
 *
 * @group      Laminas_Server
 */
class CallbackTest extends TestCase
{
    /** @var Method\Callback */
    private $callback;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->callback = new Method\Callback();
    }

    public function testClassShouldBeNullByDefault(): void
    {
        $this->assertNull($this->callback->getClass());
    }

    public function testClassShouldBeMutable(): void
    {
        $this->assertNull($this->callback->getClass());
        $this->callback->setClass('Foo');
        $this->assertEquals('Foo', $this->callback->getClass());
    }

    public function testMethodShouldBeNullByDefault(): void
    {
        $this->assertNull($this->callback->getMethod());
    }

    public function testMethodShouldBeMutable(): void
    {
        $this->assertNull($this->callback->getMethod());
        $this->callback->setMethod('foo');
        $this->assertEquals('foo', $this->callback->getMethod());
    }

    public function testFunctionShouldBeNullByDefault(): void
    {
        $this->assertNull($this->callback->getFunction());
    }

    public function testFunctionShouldBeMutable(): void
    {
        $this->assertNull($this->callback->getFunction());
        $this->callback->setFunction('foo');
        $this->assertEquals('foo', $this->callback->getFunction());
    }

    public function testFunctionMayBeCallable(): void
    {
        $callable = /**
         * @return true
         */
        function (): bool {
            return true;
        };
        $this->callback->setFunction($callable);
        $this->assertEquals($callable, $this->callback->getFunction());
    }

    public function testTypeShouldBeNullByDefault(): void
    {
        $this->assertNull($this->callback->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        $this->assertNull($this->callback->getType());
        $this->callback->setType('instance');
        $this->assertEquals('instance', $this->callback->getType());
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
        $this->assertEquals('Foo', $test['class']);
        $this->assertEquals('bar', $test['method']);
        $this->assertEquals('instance', $test['type']);
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
        $this->assertSame($options, $test);
    }

    public function testSettingFunctionShouldSetTypeAsFunction(): void
    {
        $this->assertNull($this->callback->getType());
        $this->callback->setFunction('foo');
        $this->assertEquals('function', $this->callback->getType());
    }
}
