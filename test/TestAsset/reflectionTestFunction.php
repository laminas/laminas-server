<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\TestAsset;

// phpcs:disable
/**
 * \LaminasTest\Server\reflectionTestFunction
 *
 * Used to test reflectFunction generation of signatures
 *
 * @param string|array        $arg2
 * @param string              $arg3 Optional argument
 * @param string|struct|false $arg4 Optional argument
 *
 * @return void
 */
function reflectionTestFunction(bool $arg1, $arg2, string $arg3 = 'string', $arg4 = 'array')
{
}
// phpcs:enable
