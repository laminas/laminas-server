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

class ParameterTest extends TestCase
{
    /** @var Method\Parameter */
    private $parameter;

    protected function setUp(): void
    {
        $this->parameter = new Method\Parameter();
    }

    public function testDefaultValueShouldBeNullByDefault(): void
    {
        self::assertNull($this->parameter->getDefaultValue());
    }

    public function testDefaultValueShouldBeMutable(): void
    {
        self::assertNull($this->parameter->getDefaultValue());
        $this->parameter->setDefaultValue('foo');
        self::assertSame('foo', $this->parameter->getDefaultValue());
    }

    public function testDescriptionShouldBeEmptyStringByDefault(): void
    {
        self::assertSame('', $this->parameter->getDescription());
    }

    public function testDescriptionShouldBeMutable(): void
    {
        $message = 'This is a description';
        self::assertSame('', $this->parameter->getDescription());
        $this->parameter->setDescription($message);
        self::assertSame($message, $this->parameter->getDescription());
    }

    public function testNameShouldBeNullByDefault(): void
    {
        self::assertNull($this->parameter->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $name = 'foo';
        self::assertNull($this->parameter->getName());
        $this->parameter->setName($name);
        self::assertSame($name, $this->parameter->getName());
    }

    public function testParameterShouldBeRequiredByDefault(): void
    {
        self::assertFalse($this->parameter->isOptional());
    }

    public function testParameterShouldAllowBeingOptional(): void
    {
        self::assertFalse($this->parameter->isOptional());
        $this->parameter->setOptional(true);
        self::assertTrue($this->parameter->isOptional());
    }

    public function testTypeShouldBeMixedByDefault(): void
    {
        self::assertEquals('mixed', $this->parameter->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        $type = 'string';
        self::assertEquals('mixed', $this->parameter->getType());
        $this->parameter->setType($type);
        self::assertSame($type, $this->parameter->getType());
    }

    public function testParameterShouldSerializeToArray(): void
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $parameter    = [
            'type'         => $type,
            'name'         => $name,
            'optional'     => $optional,
            'defaultValue' => $defaultValue,
            'description'  => $description,
        ];
        $this->parameter->setType($type)
                        ->setName($name)
                        ->setOptional($optional)
                        ->setDefaultValue($defaultValue)
                        ->setDescription($description);
        $test = $this->parameter->toArray();
        self::assertEquals($parameter, $test);
    }

    public function testConstructorShouldSetObjectStateFromPassedOptions(): void
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $options      = [
            'type'         => $type,
            'name'         => $name,
            'optional'     => $optional,
            'defaultValue' => $defaultValue,
            'description'  => $description,
        ];
        $parameter    = new Method\Parameter($options);
        $test         = $parameter->toArray();
        self::assertEquals($options, $test);
    }
}
