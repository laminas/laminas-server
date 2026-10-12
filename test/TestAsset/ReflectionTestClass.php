<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server\TestAsset;

/**
 * \LaminasTest\Server\TestAsset\ReflectionTestClass -- test class reflection
 */
final class ReflectionTestClass
{
    /**
     * Constructor
     *
     * This shouldn't be reflected
     *
     * @param null|mixed $arg
     */
    public function __construct($arg = null)
    {
    }
}
