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

/**
 * Server methods metadata
 */
class Definition implements Countable, Iterator
{
    /**
     * @var array Array of \Laminas\Server\Method\Definition objects
     */
    protected $methods = [];

    /**
     * @var bool Whether or not overwriting existing methods is allowed
     */
    protected $overwriteExistingMethods = false;

    /**
     * Constructor
     *
     * @param  null|array $methods
     */
    public function __construct(?array $methods = null)
    {
        if (is_array($methods)) {
            $this->setMethods($methods);
        }
    }

    /**
     * Set flag indicating whether or not overwriting existing methods is allowed
     *
     * @param mixed $flag
     * @return $this
     */
    public function setOverwriteExistingMethods($flag): self
    {
        $this->overwriteExistingMethods = (bool) $flag;
        return $this;
    }

    /**
     * Add method to definition
     *
     * @param  array|\Laminas\Server\Method\Definition $method
     * @param  null|string $name
     * @return $this
     * @throws InvalidArgumentException if duplicate or invalid method provided
     */
    public function addMethod($method, ?string $name = null): self
    {
        if (is_array($method)) {
            $method = new Method\Definition($method);
        } elseif (! $method instanceof Method\Definition) {
            throw new Exception\InvalidArgumentException('Invalid method provided');
        }

        if (null !== $name) {
            $method->setName($name);
        } else {
            $name = $method->getName();
        }
        if (null === $name) {
            throw new Exception\InvalidArgumentException('No method name provided');
        }

        if (! $this->overwriteExistingMethods && array_key_exists($name, $this->methods)) {
            throw new Exception\InvalidArgumentException(sprintf('Method by name of "%s" already exists', $name));
        }
        $this->methods[$name] = $method;
        return $this;
    }

    /**
     * Add multiple methods
     *
     * @param  array $methods Array of \Laminas\Server\Method\Definition objects or arrays
     * @return $this
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
     * @param  array $methods Array of \Laminas\Server\Method\Definition objects or arrays
     * @return $this
     */
    public function setMethods(array $methods): self
    {
        $this->clearMethods();
        $this->addMethods($methods);
        return $this;
    }

    /**
     * Does the definition have the given method?
     *
     * @param  string $method
     * @return bool
     */
    public function hasMethod(string $method): bool
    {
        return array_key_exists($method, $this->methods);
    }

    /**
     * Get a given method definition
     *
     * @param  string $method
     * @return false|\Laminas\Server\Method\Definition
     */
    public function getMethod(string $method)
    {
        if ($this->hasMethod($method)) {
            return $this->methods[$method];
        }
        return false;
    }

    /**
     * Get all method definitions
     *
     * @return array Array of \Laminas\Server\Method\Definition objects
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Remove a method definition
     *
     * @param  string $method
     * @return $this
     */
    public function removeMethod(string $method): self
    {
        if ($this->hasMethod($method)) {
            unset($this->methods[$method]);
        }
        return $this;
    }

    /**
     * Clear all method definitions
     *
     * @return $this
     */
    public function clearMethods(): self
    {
        $this->methods = [];
        return $this;
    }

    /**
     * Cast definition to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $methods = [];
        foreach ($this->getMethods() as $key => $method) {
            $methods[$key] = $method->toArray();
        }
        return $methods;
    }

    /**
     * Countable: count of methods
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->methods);
    }

    /**
     * Iterator: current item
     *
     * @return false|Method\Definition
     */
    public function current()
    {
        return current($this->methods);
    }

    /**
     * Iterator: current item key
     *
     * @return int|string|null
     */
    public function key()
    {
        return key($this->methods);
    }

    /**
     * Iterator: advance to next method
     *
     * @return false|Method\Definition
     */
    public function next()
    {
        return next($this->methods);
    }

    /**
     * Iterator: return to first method
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->methods);
    }

    /**
     * Iterator: is the current index valid?
     *
     * @return bool
     */
    public function valid(): bool
    {
        return (bool) $this->current();
    }
}
