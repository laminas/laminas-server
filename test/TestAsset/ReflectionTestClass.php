<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\TestAsset;

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
     * @param null|array $arg2
     * @return string
     */
    public function one(string $arg1, ?array $arg2 = null): string
    {
        return 'foo';
    }

    /**
     * Protected _one
     *
     * Should not be reflected
     *
     * @param string $arg1
     * @param null|array $arg2
     * @return string
     */
    // @codingStandardsIgnoreStart
    protected function _one(string $arg1, ?array $arg2 = null): string
    {
        // @codingStandardsIgnoreEnd
        return 'foo';
    }

    /**
     * Public two
     *
     * @param string $arg1
     * @param string $arg2
     * @return bool|array
     */
    public static function two(string $arg1, string $arg2)
    {
    }
}
