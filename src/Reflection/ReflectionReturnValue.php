<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

/**
 * Return value reflection
 *
 * Stores the return value type and description
 */
class ReflectionReturnValue
{
    /** @var string */
    protected $type;

    /** @var string */
    protected $description;

    public function __construct(string $type = 'mixed', string $description = '')
    {
        $this->setType($type);
        $this->setDescription($description);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $type = null): void
    {
        $this->type = $type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }
}
