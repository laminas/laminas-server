<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\TestAsset;

/**
 * \LaminasTest\Server\reflectionTestFunction
 *
 * Used to test reflectFunction generation of signatures
 *
 * @param bool $arg1
 * @param string|array $arg2
 * @param string $arg3 Optional argument
 * @param string|struct|false $arg4 Optional argument
 */
function reflectionTestFunction($arg1, $arg2, $arg3 = 'string', $arg4 = 'array'): void
{
}
