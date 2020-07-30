<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

use ReflectionParameter as PhpReflectionParameter;

use function call_user_func_array;
use function method_exists;

class ReflectionParameter
{
    protected PhpReflectionParameter $reflection;

    protected ?int $position = null;

    protected string $type;

    protected ?string $description = null;

    /**
     * Parameter name (needed for serialization)
     */
    protected string $name;

    /**
     * Declaring function name (needed for serialization)
     *
     * @todo Figure out correct type for $functionName. It is set to either a
     *       string or array of strings in the constructor.
     * @var string
     */
    protected $functionName;

    public function __construct(PhpReflectionParameter $r, string $type = 'mixed', ?string $description = null)
    {
        $this->reflection = $r;

        // Store parameters needed for (un)serialization
        $this->name         = $r->getName();
        $this->functionName = $r->getDeclaringClass()
            ? [$r->getDeclaringClass()->getName(), $r->getDeclaringFunction()->getName()]
            : $r->getDeclaringFunction()->getName();

        $this->setType($type);
        $this->setDescription($description);
    }

    /**
     * Proxy reflection calls
     *
     * @param array $args
     * @throws Exception\BadMethodCallException
     * @return mixed
     */
    public function __call(string $method, $args)
    {
        if (method_exists($this->reflection, $method)) {
            return call_user_func_array([$this->reflection, $method], $args);
        }

        throw new Exception\BadMethodCallException('Invalid reflection method');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    public function setPosition(int $index): void
    {
        $this->position = $index;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return string[]
     */
    public function __sleep(): array
    {
        return ['position', 'type', 'description', 'name', 'functionName'];
    }

    public function __wakeup(): void
    {
        $this->reflection = new PhpReflectionParameter($this->functionName, $this->name);
    }
}
