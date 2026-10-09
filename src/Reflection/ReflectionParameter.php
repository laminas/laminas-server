<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

use Deprecated;
use ReflectionException;
use ReflectionParameter as PhpReflectionParameter;

use function call_user_func_array;
use function is_string;
use function method_exists;

/**
 * Parameter Reflection
 *
 * Decorates a ReflectionParameter to allow setting the parameter type
 */
final class ReflectionParameter
{
    protected PhpReflectionParameter $reflection;

    /**
     * Parameter position
     */
    protected int $position;

    /**
     * Parameter type
     */
    protected string $type;

    /**
     * Parameter description
     */
    protected ?string $description;

    /**
     * Parameter name (needed for serialization)
     *
     * @var string
     */
    protected $name;

    /**
     * Declaring function name (needed for serialization)
     *
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
    #[Deprecated('Use __serialize instead')]
    public function __sleep(): array
    {
        return $this->__serialize();
    }

    /**
     * @return string[]
     */
    public function __serialize(): array
    {
        return [
            'position'     => $this->position,
            'type'         => $this->type,
            'description'  => $this->description,
            'name'         => $this->name,
            'functionName' => $this->functionName,
        ];
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Deprecated('Use __unserialize instead')]
    public function __wakeup(): void
    {
        $this->__unserialize($this->__serialize());
    }

    /**
     * @param array<string, mixed> $data
     * @throws ReflectionException
     */
    public function __unserialize(array $data): void
    {
        $this->position     = $data['position'] ?? '0';
        $this->type         = $data['type'] ?? 'mixed';
        $this->description  = $data['description'] ?? '';
        $this->name         = $data['name'] ?? '';
        $this->functionName = $data['functionName'] ?? '';
        $this->reflection   = new PhpReflectionParameter($this->functionName, $this->name);
    }
}
