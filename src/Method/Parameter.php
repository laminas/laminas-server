<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server\Method;

/**
 * Method parameter metadata
 */
class Parameter
{
    /**
     * Default parameter value
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Parameter description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Parameter variable name
     *
     * @var string
     */
    protected $name;

    /**
     * Is parameter optional?
     *
     * @var bool
     */
    protected $optional = false;

    /**
     * Parameter type
     *
     * @var string
     */
    protected $type = 'mixed';

    /**
     * @param null|array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from array of options
     *
     * @param  array $options
     * @return $this
     */
    public function setOptions(array $options)
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
    public function setDefaultValue($defaultValue)
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

    /**
     * Set description
     *
     * @param  string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
        return $this;
    }

    /**
     * Retrieve description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Retrieve name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set optional flag
     *
     * @param  bool $flag
     * @return $this
     */
    public function setOptional($flag)
    {
        $this->optional = (bool) $flag;
        return $this;
    }

    /**
     * Is the parameter optional?
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * Set parameter type
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    /**
     * Retrieve parameter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
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
