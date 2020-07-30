<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Method;

use Laminas\Server\Method;
use Laminas\Server\Method\Parameter;
use PHPUnit\Framework\TestCase;

class PrototypeTest extends TestCase
{
    /** @var Method\Prototype */
    private $prototype;

    protected function setUp(): void
    {
        $this->prototype = new Method\Prototype();
    }

    public function testReturnTypeShouldBeVoidByDefault(): void
    {
        self::assertEquals('void', $this->prototype->getReturnType());
    }

    public function testReturnTypeShouldBeMutable(): void
    {
        self::assertEquals('void', $this->prototype->getReturnType());
        $this->prototype->setReturnType('string');
        self::assertEquals('string', $this->prototype->getReturnType());
    }

    public function testParametersShouldBeEmptyArrayByDefault(): void
    {
        $params = $this->prototype->getParameters();
        self::assertIsArray($params);
        self::assertEmpty($params);
    }

    public function testPrototypeShouldAllowAddingSingleParameters(): void
    {
        $this->testParametersShouldBeEmptyArrayByDefault();
        $this->prototype->addParameter('string');
        $params = $this->prototype->getParameters();
        self::assertIsArray($params);
        self::assertCount(1, $params);
        self::assertEquals('string', $params[0]);

        $this->prototype->addParameter('array');
        $params = $this->prototype->getParameters();
        self::assertCount(2, $params);
        self::assertEquals('string', $params[0]);
        self::assertEquals('array', $params[1]);
    }

    public function testPrototypeShouldAllowAddingParameterObjects(): void
    {
        $parameter = new Method\Parameter([
            'type' => 'string',
            'name' => 'foo',
        ]);
        $this->prototype->addParameter($parameter);
        self::assertSame($parameter, $this->prototype->getParameter('foo'));
    }

    public function testPrototypeShouldAllowFetchingParameterByNameOrIndex(): void
    {
        $parameter = new Method\Parameter([
            'type' => 'string',
            'name' => 'foo',
        ]);
        $this->prototype->addParameter($parameter);
        $test1 = $this->prototype->getParameter('foo');
        $test2 = $this->prototype->getParameter(0);
        self::assertSame($test1, $test2);
        self::assertSame($parameter, $test1);
        self::assertSame($parameter, $test2);
    }

    public function testPrototypeShouldAllowRetrievingParameterObjects(): void
    {
        $this->prototype->addParameters(['string', 'array']);
        $parameters = $this->prototype->getParameterObjects();
        foreach ($parameters as $parameter) {
            self::assertInstanceOf(Parameter::class, $parameter);
        }
    }

    public function testPrototypeShouldAllowAddingMultipleParameters(): void
    {
        $this->testParametersShouldBeEmptyArrayByDefault();
        $params = [
            'string',
            'array',
        ];
        $this->prototype->addParameters($params);
        $test = $this->prototype->getParameters();
        self::assertSame($params, $test);
    }

    public function testSetParametersShouldOverwriteParameters(): void
    {
        $this->testPrototypeShouldAllowAddingMultipleParameters();
        $params = [
            'bool',
            'base64',
            'struct',
        ];
        $this->prototype->setParameters($params);
        $test = $this->prototype->getParameters();
        self::assertSame($params, $test);
    }

    public function testPrototypeShouldSerializeToArray(): void
    {
        $return = 'string';
        $params = [
            'bool',
            'base64',
            'struct',
        ];
        $this->prototype->setReturnType($return)
                        ->setParameters($params);
        $test = $this->prototype->toArray();
        self::assertEquals($return, $test['returnType']);
        self::assertEquals($params, $test['parameters']);
    }

    public function testConstructorShouldSetObjectStateFromOptions(): void
    {
        $options   = [
            'returnType' => 'string',
            'parameters' => [
                'bool',
                'base64',
                'struct',
            ],
        ];
        $prototype = new Method\Prototype($options);
        $test      = $prototype->toArray();
        self::assertSame($options, $test);
    }
}
