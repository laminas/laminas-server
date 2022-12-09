<?php

namespace LaminasTest\Server\Reflection\TestAsset;

use function is_array;

/**
 * Test function for reflection unit tests
 *
 * @param string $var1 Some description
 * @param string|array $var2
 * @param array $var3
 * @return null|array
 */
function function1($var1, $var2, $var3 = null): ?array
{
    // The body of this is nonsense written to appease Psalm.
    if (is_array($var2) && is_array($var3)) {
        return $var3;
    }

    return null;
}

/**
 * Test function for reflection unit tests; test what happens when no return
 * value or params specified in docblock.
 */
function function2(): void
{
}

/**
 * @param  string $var1
 * @return void
 */
function function3($var1)
{
}
