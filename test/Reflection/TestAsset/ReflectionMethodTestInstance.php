<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Reflection\TestAsset;

use LaminasTest\Server\Reflection\ReflectionMethodTest;
use Override;

final class ReflectionMethodTestInstance implements ReflectionMethodInterface
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    #[Override]
    public function testMethod(ReflectionMethodTest $reflectionMethodTest, array $anything): void
    {
        // it doesn`t matter
    }
}
