<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Cache;

use Laminas\Server\Cache;
use Laminas\Server\Definition;
use Laminas\Server\Method\Callback;
use Laminas\Server\Method\Definition as MethodDefinition;
use Laminas\Server\Server;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use function file_get_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use function unserialize;

class CacheTest extends TestCase
{
    /** @var string */
    private $cacheFile;

    public function tearDown(): void
    {
        if ($this->cacheFile) {
            unlink($this->cacheFile);
            $this->cacheFile = null;
        }
        $this->resetSkipMethods();
    }

    public function resetSkipMethods(array $methods = []): void
    {
        $r = new ReflectionProperty(Cache::class, 'skipMethods');
        $r->setAccessible(true);
        $r->setValue(null, $methods);
    }

    public function testCacheCanAcceptAServerReturningAnArrayOfFunctions(): void
    {
        $functions = [
            'strpos' => 'strpos',
            'substr' => 'substr',
            'strlen' => 'strlen',
        ];
        $server    = $this->createMock(Server::class);
        $server->method('getFunctions')->willReturn($functions);

        $this->cacheFile = tempnam(sys_get_temp_dir(), 'zs');

        $this->assertTrue(Cache::save($this->cacheFile, $server));

        $data = file_get_contents($this->cacheFile);
        $data = unserialize($data);
        $this->assertEquals($functions, $data);
    }

    public function testCacheCanAcceptAServerReturningADefinition(): void
    {
        $definition = new Definition();
        foreach (['strpos', 'substr', 'strlen'] as $function) {
            $callback = new Callback();
            $callback->setFunction($function);

            $method = new MethodDefinition();
            $method->setName($function);
            $method->setCallback($callback);

            $definition->addMethod($method);
        }

        $server = $this->createMock(Server::class);
        $server->method('getFunctions')->willReturn($definition);

        $this->cacheFile = tempnam(sys_get_temp_dir(), 'zs');

        $this->assertTrue(Cache::save($this->cacheFile, $server));

        $data = file_get_contents($this->cacheFile);
        $data = unserialize($data);
        $this->assertEquals($definition, $data);
    }

    public function testCacheSkipsMethodsWhenGivenAnArrayOfFunctions(): void
    {
        $this->resetSkipMethods(['substr']);

        $functions = [
            'strpos' => 'strpos',
            'substr' => 'substr',
            'strlen' => 'strlen',
        ];
        $server    = $this->createMock(Server::class);
        $server->method('getFunctions')->willReturn($functions);

        $this->cacheFile = tempnam(sys_get_temp_dir(), 'zs');

        $this->assertTrue(Cache::save($this->cacheFile, $server));

        $data = file_get_contents($this->cacheFile);
        $data = unserialize($data);

        $expected = $functions;
        unset($expected['substr']);

        $this->assertEquals($expected, $data);
    }

    public function testCacheSkipsMethodsWhenGivenADefinition(): void
    {
        $this->resetSkipMethods(['substr']);

        $definition = new Definition();
        foreach (['strpos', 'substr', 'strlen'] as $function) {
            $callback = new Callback();
            $callback->setFunction($function);

            $method = new MethodDefinition();
            $method->setName($function);
            $method->setCallback($callback);

            $definition->addMethod($method);
        }

        $server = $this->createMock(Server::class);
        $server->method('getFunctions')->willReturn($definition);

        $this->cacheFile = tempnam(sys_get_temp_dir(), 'zs');

        $this->assertTrue(Cache::save($this->cacheFile, $server));

        $data = file_get_contents($this->cacheFile);
        $data = unserialize($data);

        $expected = ['strpos', 'strlen'];

        $actual = [];
        foreach ($data as $method) {
            $actual[] = $method->getName();
        }

        $this->assertEquals($expected, $actual);
    }
}
