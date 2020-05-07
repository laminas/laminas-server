<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Laminas\Server;

class Callback
{
    /**
     * @var string
     */
    protected $class;

    /**
     * Function name or callable for function callback
     *
     * @var string|callable
     */
    protected $function;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * Valid callback types
     *
     * @var array
     */
    protected $types = ['function', 'static', 'instance'];

    public function __construct(?array $options = null)
    {
        if ((null !== $options) && is_array($options)) {
            $this->setOptions($options);
        }
    }

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
     *
     * @param  string|object $class
     * @return $this
     */
    public function setClass($class): self
    {
        if (is_object($class)) {
            $class = get_class($class);
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
     *
     * @param  string|callable $function
     * @return $this
     */
    public function setFunction($function): self
    {
        $this->function = is_callable($function) ? $function : (string) $function;
        $this->setType('function');
        return $this;
    }

    /**
     * Get callback function
     *
     * @return null|string|callable
     */
    public function getFunction()
    {
        return $this->function;
    }

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
        $type = $this->getType();
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
