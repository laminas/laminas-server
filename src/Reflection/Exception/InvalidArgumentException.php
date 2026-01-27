<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace Laminas\Server\Reflection\Exception;

use Laminas\Server\Exception;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class InvalidArgumentException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
