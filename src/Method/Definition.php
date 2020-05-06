<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Laminas\Server;

/**
 * Method definition metadata
 */
class Definition
{
    /**
     * @var null|Callback
     */
    protected $callback;

    /**
     * @var array
     */
    protected $invokeArguments = [];

    /**
     * @var string
     */
    protected $methodHelp = '';

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var null|object
     */
    protected $object;

    /**
     * @var Prototype[]
     */
    protected $prototypes = [];

    public function __construct(?array $options = null)
    {
        if ((null !== $options) && is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from options
     *
     * @param  array $options
     * @return $this
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
     * Set method name
     *
     * @param  string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get method name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set method callback
     *
     * @param  array|Callback $callback
     * @throws Server\Exception\InvalidArgumentException
     * @return $this
     */
    public function setCallback($callback): self
    {
        if (is_array($callback)) {
            $callback = new Callback($callback);
        } elseif (! $callback instanceof Callback) {
            throw new Server\Exception\InvalidArgumentException('Invalid method callback provided');
        }
        $this->callback = $callback;
        return $this;
    }

    /**
     * Get method callback
     *
     * @return null|Callback
     */
    public function getCallback(): ?Callback
    {
        return $this->callback;
    }

    /**
     * Add prototype to method definition
     *
     * @param  array|Prototype $prototype
     * @throws Server\Exception\InvalidArgumentException
     * @return $this
     */
    public function addPrototype($prototype): self
    {
        if (is_array($prototype)) {
            $prototype = new Prototype($prototype);
        } elseif (! $prototype instanceof Prototype) {
            throw new Server\Exception\InvalidArgumentException('Invalid method prototype provided');
        }
        $this->prototypes[] = $prototype;
        return $this;
    }

    /**
     * Add multiple prototypes at once
     *
     * @param  Prototype[] $prototypes
     * @return $this
     */
    public function addPrototypes(array $prototypes): self
    {
        foreach ($prototypes as $prototype) {
            $this->addPrototype($prototype);
        }
        return $this;
    }

    /**
     * Set all prototypes at once (overwrites)
     *
     * @param  Prototype[] $prototypes
     * @return $this
     */
    public function setPrototypes(array $prototypes): self
    {
        $this->prototypes = [];
        $this->addPrototypes($prototypes);
        return $this;
    }

    /**
     * Get all prototypes
     *
     * @return Prototype[] $prototypes
     */
    public function getPrototypes(): array
    {
        return $this->prototypes;
    }

    /**
     * Set method help
     *
     * @param  string $methodHelp
     * @return $this
     */
    public function setMethodHelp(string $methodHelp): self
    {
        $this->methodHelp = $methodHelp;
        return $this;
    }

    /**
     * Get method help
     *
     * @return string
     */
    public function getMethodHelp(): string
    {
        return $this->methodHelp;
    }

    /**
     * Set object to use with method calls
     *
     * @param  object $object
     * @throws Server\Exception\InvalidArgumentException
     * @return $this
     */
    public function setObject(object $object): self
    {
        if (! is_object($object) && (null !== $object)) {
            throw new Server\Exception\InvalidArgumentException(sprintf(
                'Invalid object passed to %s',
                __METHOD__
            ));
        }
        $this->object = $object;
        return $this;
    }

    /**
     * Get object to use with method calls
     *
     * @return null|object
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * Set invoke arguments
     *
     * @param  array $invokeArguments
     * @return $this
     */
    public function setInvokeArguments(array $invokeArguments): self
    {
        $this->invokeArguments = $invokeArguments;
        return $this;
    }

    /**
     * Retrieve invoke arguments
     *
     * @return array
     */
    public function getInvokeArguments(): array
    {
        return $this->invokeArguments;
    }

    /**
     * Serialize to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $prototypes = $this->getPrototypes();
        $signatures = [];
        foreach ($prototypes as $prototype) {
            $signatures[] = $prototype->toArray();
        }

        return [
            'name'            => $this->getName(),
            'callback'        => $this->getCallback()->toArray(),
            'prototypes'      => $signatures,
            'methodHelp'      => $this->getMethodHelp(),
            'invokeArguments' => $this->getInvokeArguments(),
            'object'          => $this->getObject(),
        ];
    }
}
