<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Laminas\Server;

use function is_array;
use function method_exists;
use function ucfirst;

/**
 * @psalm-import-type CallbackToArrayShape from Callback
 * @psalm-import-type PrototypeToArrayShape from Prototype
 * @psalm-type MethodDefinitionToArrayShape = array{
 *     name: string|null,
 *     callback: array{}|CallbackToArrayShape,
 *     prototypes: list<PrototypeToArrayShape>,
 *     methodHelp: string,
 *     invokeArguments: array,
 *     object: object|null,
 * }
 */
class Definition
{
    /** @var null|Callback */
    protected $callback;

    /** @var array */
    protected $invokeArguments = [];

    /** @var string */
    protected $methodHelp = '';

    /** @var null|string */
    protected $name;

    /** @var null|object */
    protected $object;

    /** @var list<Prototype> */
    protected $prototypes = [];

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
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
     * @psalm-param Prototype|array<string, mixed> $prototype
     */
    public function addPrototype($prototype): self
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
     * @return $this
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
     * @return $this
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
     * @return list<Prototype>
     */
    public function getPrototypes(): array
    {
        return $this->prototypes;
    }

    public function setMethodHelp(string $methodHelp): self
    {
        $this->methodHelp = $methodHelp;
        return $this;
    }

    public function getMethodHelp(): string
    {
        return $this->methodHelp;
    }

    public function setObject(object $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function setInvokeArguments(array $invokeArguments): self
    {
        $this->invokeArguments = $invokeArguments;
        return $this;
    }

    public function getInvokeArguments(): array
    {
        return $this->invokeArguments;
    }

    /** @return MethodDefinitionToArrayShape */
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
