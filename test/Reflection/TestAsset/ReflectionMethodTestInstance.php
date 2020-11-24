<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection\TestAsset;

use LaminasTest\Server\Reflection\ReflectionMethodTest;

class ReflectionMethodTestInstance implements ReflectionMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function testMethod(ReflectionMethodTest $reflectionMethodTest, array $anything): void
    {
        // it doesn`t matter
    }
}
