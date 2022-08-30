<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Exception;

use RuntimeException as PhpRuntimeException;

class RuntimeException extends PhpRuntimeException implements ExceptionInterface
{
}
