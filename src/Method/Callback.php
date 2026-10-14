<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Closure;
use Laminas\Server;

use function in_array;
use function is_array;
use function is_object;
use function method_exists;
use function sprintf;
use function ucfirst;

/**
 * Method callback metadata
 */
final class Callback
{
    protected ?string $class = null;

    /** @var string|Closure|null Function name or callable for function callback */
    protected string|Closure|null $function = null;

    protected ?string $method = null;

    protected ?string $type = null;

    /** @var array Valid callback types */
    protected array $types = ['function', 'static', 'instance'];

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from array of options
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set callback class
     */
    public function setClass(string|object $class): self
    {
        if (is_object($class)) {
            $class = $class::class;
        }
        $this->class = $class;
        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * Set callback function
     */
    public function setFunction(string|Closure $function): self
    {
        $this->function = $function;
        $this->setType('function');
        return $this;
    }

    /**
     * Get callback function
     */
    public function getFunction(): string|callable|null
    {
        return $this->function;
    }

    /**
     * Set callback class method
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Set callback type
     *
     * @throws Server\Exception\InvalidArgumentException
     */
    public function setType(string $type): self
    {
        if (! in_array($type, $this->types, true)) {
            throw new Server\Exception\InvalidArgumentException(sprintf(
                'Invalid method callback type "%s" passed to %s',
                $type,
                __METHOD__
            ));
        }
        $this->type = $type;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        $type  = $this->getType();
        $array = [
            'type' => $type,
        ];
        if ('function' === $type) {
            $array['function'] = $this->getFunction();
        } else {
            $array['class']  = $this->getClass();
            $array['method'] = $this->getMethod();
        }
        return $array;
    }
}
