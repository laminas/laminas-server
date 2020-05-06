<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection;
use Laminas\Server\Reflection\ReflectionReturnValue;

use PHPUnit\Framework\TestCase;

class ReflectionReturnValueTest extends TestCase
{
    public function testConstructor(): void
    {
        $obj = new Reflection\ReflectionReturnValue();
        $this->assertInstanceOf(ReflectionReturnValue::class, $obj);
    }

    public function testGetType(): void
    {
        $obj = new Reflection\ReflectionReturnValue();
        $this->assertEquals('mixed', $obj->getType());

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    public function testSetType(): void
    {
        $obj = new Reflection\ReflectionReturnValue();

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    public function testGetDescription(): void
    {
        $obj = new Reflection\ReflectionReturnValue('string', 'Some description');
        $this->assertEquals('Some description', $obj->getDescription());

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }

    public function testSetDescription(): void
    {
        $obj = new Reflection\ReflectionReturnValue();

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }
}
