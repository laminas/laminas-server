<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public static function two($arg1, $arg2): void
    {
    }
}
