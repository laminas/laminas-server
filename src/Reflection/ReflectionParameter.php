<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace Laminas\Server\Reflection;

use Deprecated;
use ReflectionException;

use function call_user_func_array;
use function is_string;
use function method_exists;

/**
 * Parameter Reflection
 *
 * Decorates a ReflectionParameter to allow setting the parameter type
 *
 * @final This class should not be extended
 */
class ReflectionParameter
{
    /** @var \ReflectionParameter */
    protected $reflection;

    /**
     * Parameter position
     *
     * @var int
     */
    protected $position;

    /**
     * Parameter type
     *
     * @var string
     */
    protected $type;

    /**
     * Parameter description
     *
     * @var string
     */
    protected $description;

    /**
     * Parameter name (needed for serialization)
     *
     * @var string
     */
    protected $name;

    /**
     * Declaring function name (needed for serialization)
     *
     * @var string
     */
    protected $functionName;

    /**
     * Constructor
     *
     * @param string $type Parameter type
     * @param string $description Parameter description
     */
    public function __construct(\ReflectionParameter $r, $type = 'mixed', $description = '')
    {
        $this->reflection = $r;

        // Store parameters needed for (un)serialization
        $this->name         = $r->getName();
        $this->functionName = $r->getDeclaringClass()
            ? [$r->getDeclaringClass()->getName(), $r->getDeclaringFunction()->getName()]
            : $r->getDeclaringFunction()->getName();

        $this->setType($type);
        $this->setDescription($description);
    }

    /**
     * Proxy reflection calls
     *
     * @param string $method
     * @param array $args
     * @throws Exception\BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this->reflection, $method)) {
            return call_user_func_array([$this->reflection, $method], $args);
        }

        throw new Exception\BadMethodCallException('Invalid reflection method');
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
     * Set parameter type
     *
     * @param string|null $type
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setType($type)
    {
        if (! is_string($type) && (null !== $type)) {
            throw new Exception\InvalidArgumentException('Invalid parameter type');
        }

        $this->type = $type;
    }

    /**
     * Retrieve parameter description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set parameter description
     *
     * @param string|null $description
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setDescription($description)
    {
        if (! is_string($description) && (null !== $description)) {
            throw new Exception\InvalidArgumentException('Invalid parameter description');
        }

        $this->description = $description;
    }

    /**
     * Set parameter position
     *
     * @param int $index
     * @return void
     */
    public function setPosition($index)
    {
        $this->position = $index;
    }

    /**
     * Return parameter position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string[]
     */
    #[Deprecated('Use __serialize instead')]
    public function __sleep()
    {
        return $this->__serialize();
    }

    /**
     * @return string[]
     */
    public function __serialize(): array
    {
        return [
            'position'     => $this->position,
            'type'         => $this->type,
            'description'  => $this->description,
            'name'         => $this->name,
            'functionName' => $this->functionName,
        ];
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    #[Deprecated('Use __unserialize instead')]
    public function __wakeup()
    {
        $this->__unserialize($this->__serialize());
    }

    /**
     * @param array<string, mixed> $data
     * @throws ReflectionException
     */
    public function __unserialize(array $data): void
    {
        $this->position     = $data['position'] ?? '0';
        $this->type         = $data['type'] ?? 'mixed';
        $this->description  = $data['description'] ?? '';
        $this->name         = $data['name'] ?? '';
        $this->functionName = $data['functionName'] ?? '';
        $this->reflection   = new \ReflectionParameter($this->functionName, $this->name);
    }
}
