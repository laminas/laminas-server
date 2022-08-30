<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;

class InvalidArgumentException extends PhpInvalidArgumentException implements ExceptionInterface
{
}
