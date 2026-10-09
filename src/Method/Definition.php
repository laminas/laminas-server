<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Laminas\Server;
use Laminas\Server\Method\Callback;
use Laminas\Server\Method\Prototype;

use function is_array;
use function is_object;
use function method_exists;
use function sprintf;
use function ucfirst;

/**
 * Method definition metadata
 */
final class Definition
{
    /** @var null|Callback */
    protected ?Callback $callback;

    protected array $invokeArguments = [];

    protected string $methodHelp = '';

    protected ?string $name;

    protected ?object $object;

    /** @var Prototype[] */
    protected array $prototypes = [];

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from options
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
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set method callback
     *
     * @throws Server\Exception\InvalidArgumentException
     */
    public function setCallback(array|Callback $callback): self
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
     */
    public function getCallback(): ?Callback
    {
        return $this->callback;
    }

    /**
     * Add prototype to method definition
     *
     * @throws Server\Exception\InvalidArgumentException
     * @psalm-param Prototype|array<string, mixed> $prototype
     */
    public function addPrototype(array|Prototype $prototype): self
    {
        if (is_array($prototype)) {
            $prototype = new Prototype($prototype);
        }

        $this->prototypes[] = $prototype;
        return $this;
    }

    /**
     * Add multiple prototypes at once
     *
     * @param  Prototype[] $prototypes
     * @psalm-param array<array-key, Prototype|array<string, mixed>> $prototypes
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
     * @psalm-param array<array-key, Prototype|array<string, mixed>> $prototypes
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
     * @return Prototype[]
     */
    public function getPrototypes(): array
    {
        return $this->prototypes;
    }

    /**
     * Set method help
     */
    public function setMethodHelp(string $methodHelp): self
    {
        $this->methodHelp = $methodHelp;
        return $this;
    }

    public function getMethodHelp(): string
    {
        return $this->methodHelp;
    }

    /**
     * Set object to use with method calls
     *
     * @throws Server\Exception\InvalidArgumentException
     */
    public function setObject(object $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * Set invoke arguments
     */
    public function setInvokeArguments(array $invokeArguments): self
    {
        $this->invokeArguments = $invokeArguments;
        return $this;
    }

    public function getInvokeArguments(): array
    {
        return $this->invokeArguments;
    }

    public function toArray(): array
    {
        $prototypes = $this->getPrototypes();
        $signatures = [];
        foreach ($prototypes as $prototype) {
            $signatures[] = $prototype->toArray();
        }

        $callback = $this->getCallback();

        return [
            'name'            => $this->getName(),
            'callback'        => $callback ? $callback->toArray() : [],
            'prototypes'      => $signatures,
            'methodHelp'      => $this->getMethodHelp(),
            'invokeArguments' => $this->getInvokeArguments(),
            'object'          => $this->getObject(),
        ];
    }
}
