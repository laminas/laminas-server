<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

use Webmozart\Assert\Assert;

use function array_key_exists;
use function count;
use function is_array;
use function is_numeric;
use function is_string;
use function method_exists;
use function ucfirst;

class Prototype
{
    /** @var string */
    protected $returnType = 'void';

    /**
     * Map parameter names to parameter index
     *
     * @var array
     * @psalm-var array<string, int>
     */
    protected $parameterNameMap = [];

    /** @var Parameter[] */
    protected $parameters = [];

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function setReturnType(string $returnType): self
    {
        $this->returnType = $returnType;
        return $this;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    /**
     * Add a parameter
     *
     * @param  string|Parameter $parameter
     * @return $this
     */
    public function addParameter($parameter): self
    {
        if ($parameter instanceof Parameter) {
            $this->parameters[] = $parameter;
            $name               = $parameter->getName();
            Assert::notNull($name);
            $this->parameterNameMap[$name] = count($this->parameters) - 1;
        } else {
            $parameter          = new Parameter([
                'type' => $parameter,
            ]);
            $this->parameters[] = $parameter;
        }
        return $this;
    }

    public function addParameters(array $parameters): self
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }
        return $this;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters       = [];
        $this->parameterNameMap = [];
        $this->addParameters($parameters);
        return $this;
    }

    /**
     * Retrieve parameters as list of types
     */
    public function getParameters(): array
    {
        $types = [];
        foreach ($this->parameters as $parameter) {
            $types[] = $parameter->getType();
        }
        return $types;
    }

    public function getParameterObjects(): array
    {
        return $this->parameters;
    }

    /**
     * Retrieve a single parameter by name or index
     *
     * @param  string|int $index
     */
    public function getParameter($index): ?Parameter
    {
        if (! is_string($index) && ! is_numeric($index)) {
            return null;
        }

        if (array_key_exists($index, $this->parameterNameMap)) {
            $index = $this->parameterNameMap[$index];
        }

        if (array_key_exists($index, $this->parameters)) {
            return $this->parameters[$index];
        }

        return null;
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

    public function toArray(): array
    {
        return [
            'returnType' => $this->getReturnType(),
            'parameters' => $this->getParameters(),
        ];
    }
}
