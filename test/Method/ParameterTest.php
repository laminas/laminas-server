<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Method;

use Laminas\Server\Method;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Laminas\Server\Method\Parameter
 *
 * @group      Laminas_Server
 */
class ParameterTest extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp() : void
    {
        $this->parameter = new Method\Parameter();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown() : void
    {
    }

    public function testDefaultValueShouldBeNullByDefault(): void
    {
        $this->assertNull($this->parameter->getDefaultValue());
    }

    public function testDefaultValueShouldBeMutable(): void
    {
        $this->assertNull($this->parameter->getDefaultValue());
        $this->parameter->setDefaultValue('foo');
        $this->assertSame('foo', $this->parameter->getDefaultValue());
    }

    public function testDescriptionShouldBeEmptyStringByDefault(): void
    {
        $this->assertSame('', $this->parameter->getDescription());
    }

    public function testDescriptionShouldBeMutable(): void
    {
        $message = 'This is a description';
        $this->assertSame('', $this->parameter->getDescription());
        $this->parameter->setDescription($message);
        $this->assertSame($message, $this->parameter->getDescription());
    }

    public function testNameShouldBeNullByDefault(): void
    {
        $this->assertNull($this->parameter->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $name = 'foo';
        $this->assertNull($this->parameter->getName());
        $this->parameter->setName($name);
        $this->assertSame($name, $this->parameter->getName());
    }

    public function testParameterShouldBeRequiredByDefault(): void
    {
        $this->assertFalse($this->parameter->isOptional());
    }

    public function testParameterShouldAllowBeingOptional(): void
    {
        $this->assertFalse($this->parameter->isOptional());
        $this->parameter->setOptional(true);
        $this->assertTrue($this->parameter->isOptional());
    }

    public function testTypeShouldBeMixedByDefault(): void
    {
        $this->assertEquals('mixed', $this->parameter->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        $type = 'string';
        $this->assertEquals('mixed', $this->parameter->getType());
        $this->parameter->setType($type);
        $this->assertSame($type, $this->parameter->getType());
    }

    public function testParameterShouldSerializeToArray(): void
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $parameter    = compact('type', 'name', 'optional', 'defaultValue', 'description');
        $this->parameter->setType($type)
                        ->setName($name)
                        ->setOptional($optional)
                        ->setDefaultValue($defaultValue)
                        ->setDescription($description);
        $test = $this->parameter->toArray();
        $this->assertEquals($parameter, $test);
    }

    public function testConstructorShouldSetObjectStateFromPassedOptions(): void
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $options      = compact('type', 'name', 'optional', 'defaultValue', 'description');
        $parameter    = new Method\Parameter($options);
        $test         = $parameter->toArray();
        $this->assertEquals($options, $test);
    }
}
