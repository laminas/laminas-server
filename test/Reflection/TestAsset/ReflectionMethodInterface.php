<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Reflection\TestAsset;

use LaminasTest\Server\Reflection\ReflectionMethodTest;

interface ReflectionMethodInterface
{
    /**
     * Test method
     *
     * @param ReflectionMethodTest $reflectionMethodTest Reflection method
     * @param array                $anything             Some array information
     */
    public function testMethod(ReflectionMethodTest $reflectionMethodTest, array $anything);
}
