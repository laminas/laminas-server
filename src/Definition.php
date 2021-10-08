<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server;

use Countable;
use Iterator;
use Laminas\Server\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_key_exists;
use function count;
use function current;
use function is_array;
use function is_numeric;
use function key;
use function next;
use function reset;
use function sprintf;

class Definition implements Countable, Iterator
{
    /** @var Method\Definition[] */
    protected $methods = [];

    /** @var bool */
    protected $overwriteExistingMethods = false;

    /**
     * @psalm-param null|array<array-key, Method\Definition|array<string, mixed>> $methods
     */
    public function __construct(?array $methods = null)
    {
        if (is_array($methods)) {
            $this->setMethods($methods);
        }
    }

    public function setOverwriteExistingMethods(bool $flag): self
    {
        $this->overwriteExistingMethods = $flag;
        return $this;
    }

    /**
     * Add method to definition
     *
     * @param  array|Method\Definition $method
     * @return $this
     * @throws InvalidArgumentException If duplicate or invalid method provided.
     * @psalm-param Method\Definition|array<string, mixed> $method
     */
    public function addMethod($method, ?string $name = null): self
    {
        if (is_array($method)) {
            $method = new Method\Definition($method);
        }

        if (is_numeric($name)) {
            $name = null;
        }

        if (null !== $name) {
            $method->setName($name);
        } else {
            $name = $method->getName();
        }
        if (null === $name) {
            throw new InvalidArgumentException('No method name provided');
        }

        if (! $this->overwriteExistingMethods && array_key_exists($name, $this->methods)) {
            throw new InvalidArgumentException(sprintf('Method by name of "%s" already exists', $name));
        }
        $this->methods[$name] = $method;
        return $this;
    }

    /**
     * @param  Method\Definition[] $methods
     * @psalm-param array<array-key, Method\Definition|array<string, mixed>> $methods
     */
    public function addMethods(array $methods): self
    {
        foreach ($methods as $key => $method) {
            if (is_numeric($key)) {
                $key = null;
            }
            $this->addMethod($method, $key);
        }
        return $this;
    }

    /**
     * Set all methods at once (overwrite)
     *
     * @param  Method\Definition[] $methods
     * @return $this
     * @psalm-param array<array-key, Method\Definition|array<string, mixed>> $methods
     */
    public function setMethods(array $methods): self
    {
        $this->clearMethods();
        $this->addMethods($methods);
        return $this;
    }

    public function hasMethod(string $method): bool
    {
        return array_key_exists($method, $this->methods);
    }

    /**
     * Get a given method definition
     *
     * @return bool|Method\Definition
     */
    public function getMethod(string $method)
    {
        if ($this->hasMethod($method)) {
            return $this->methods[$method];
        }
        return false;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function removeMethod(string $method): self
    {
        if ($this->hasMethod($method)) {
            unset($this->methods[$method]);
        }
        return $this;
    }

    public function clearMethods(): self
    {
        $this->methods = [];
        return $this;
    }

    public function toArray(): array
    {
        $methods = [];
        foreach ($this->getMethods() as $key => $method) {
            $methods[$key] = $method->toArray();
        }
        return $methods;
    }

    public function count(): int
    {
        return count($this->methods);
    }

    /**
     * Iterator: current item
     *
     * @return Method\Definition
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->methods);
    }

    /**
     * Iterator: current item key
     *
     * @return int|string|null
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->methods);
    }

    /**
     * Iterator: advance to next method
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->methods);
    }

    public function rewind(): void
    {
        reset($this->methods);
    }

    public function valid(): bool
    {
        return (bool) $this->current();
    }
}
