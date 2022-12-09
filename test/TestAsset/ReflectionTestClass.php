<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\TestAsset;

/**
 * \LaminasTest\Server\TestAsset\ReflectionTestClass -- test class reflection
 */
class ReflectionTestClass
{
    /**
     * Constructor
     *
     * This shouldn't be reflected
     *
     * @param mixed $arg
     */
    public function __construct($arg = null)
    {
    }

    /**
     * Public one
     *
     * @param string $arg1
     * @param array $arg2
     */
    public function one($arg1, $arg2 = null): void
    {
    }

    /**
     * Protected _one
     *
     * Should not be reflected
     *
     * @param string $arg1
     * @param array $arg2
     */
    protected function _one($arg1, $arg2 = null): void
    {
        // @codingStandardsIgnoreEnd
    }

    /**
     * Public two
     *
     * @param string $arg1
     * @param string $arg2
     */
    public static function two($arg1, $arg2): void
    {
    }
}
