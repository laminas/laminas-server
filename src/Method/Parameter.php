<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Method;

class Parameter
{
    /** @var mixed */
    protected $defaultValue;

    /** @var string */
    protected $description = '';

    /** @var null|string */
    protected $name;

    /** @var bool */
    protected $optional = false;

    /** @var string */
    protected $type = 'mixed';

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

    /**
     * Set default value
     *
     * @param  mixed $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Retrieve default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setOptional(bool $flag): self
    {
        $this->optional = $flag;
        return $this;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'type'         => $this->getType(),
            'name'         => $this->getName(),
            'optional'     => $this->isOptional(),
            'defaultValue' => $this->getDefaultValue(),
            'description'  => $this->getDescription(),
        ];
    }
}
