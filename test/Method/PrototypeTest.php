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

/**
 * Test class for \Laminas\Server\Method\Prototype
 *
 * @group      Laminas_Server
 */
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
        $this->assertEquals('void', $this->prototype->getReturnType());
    }

    public function testReturnTypeShouldBeMutable(): void
    {
        $this->assertEquals('void', $this->prototype->getReturnType());
        $this->prototype->setReturnType('string');
        $this->assertEquals('string', $this->prototype->getReturnType());
    }

    public function testParametersShouldBeEmptyArrayByDefault(): void
    {
        $params = $this->prototype->getParameters();
        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }

    public function testPrototypeShouldAllowAddingSingleParameters(): void
    {
        $this->testParametersShouldBeEmptyArrayByDefault();
        $this->prototype->addParameter('string');
        $params = $this->prototype->getParameters();
        $this->assertIsArray($params);
        $this->assertCount(1, $params);
        $this->assertEquals('string', $params[0]);

        $this->prototype->addParameter('array');
        $params = $this->prototype->getParameters();
        $this->assertCount(2, $params);
        $this->assertEquals('string', $params[0]);
        $this->assertEquals('array', $params[1]);
    }

    public function testPrototypeShouldAllowAddingParameterObjects(): void
    {
        $parameter = new Method\Parameter([
            'type' => 'string',
            'name' => 'foo',
        ]);
        $this->prototype->addParameter($parameter);
        $this->assertSame($parameter, $this->prototype->getParameter('foo'));
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
        $this->assertSame($test1, $test2);
        $this->assertSame($parameter, $test1);
        $this->assertSame($parameter, $test2);
    }

    public function testPrototypeShouldAllowRetrievingParameterObjects(): void
    {
        $this->prototype->addParameters(['string', 'array']);
        $parameters = $this->prototype->getParameterObjects();
        foreach ($parameters as $parameter) {
            $this->assertInstanceOf(Parameter::class, $parameter);
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
        $this->assertSame($params, $test);
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
        $this->assertSame($params, $test);
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
        $this->assertEquals($return, $test['returnType']);
        $this->assertEquals($params, $test['parameters']);
    }

    public function testConstructorShouldSetObjectStateFromOptions(): void
    {
        $options = [
            'returnType' => 'string',
            'parameters' => [
                'bool',
                'base64',
                'struct',
            ],
        ];
        $prototype = new Method\Prototype($options);
        $test = $prototype->toArray();
        $this->assertSame($options, $test);
    }
}
