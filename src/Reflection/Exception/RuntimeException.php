<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace Laminas\Server\Reflection\Exception;

use Laminas\Server\Exception;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class RuntimeException extends Exception\RuntimeException implements ExceptionInterface
{
}
