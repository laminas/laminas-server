<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
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
     * @param null|mixed $arg
     */
    public function __construct($arg = null)
    {
    }

    /**
     * Public one
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
     * @param null|array $arg2
     */
    // @codingStandardsIgnoreStart
    protected function _one(string $arg1, ?array $arg2 = null): string
    {
        // @codingStandardsIgnoreEnd
        return 'foo';
    }

    public static function two(string $arg1, string $arg2): void
    {
    }
}
