<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Reflection\TestAsset;

use LaminasTest\Server\Reflection\ReflectionMethodTest;

class ReflectionMethodTestInstance implements ReflectionMethodInterface
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function testMethod(ReflectionMethodTest $reflectionMethodTest, array $anything)
    {
        // it doesn`t matter
    }
}
