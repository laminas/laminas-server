<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server\Reflection;

use ReflectionClass as PhpReflectionClass;

/**
 * Class/Object reflection
 *
 * Proxies calls to a ReflectionClass object, and decorates getMethods() by
 * creating its own list of {@link ReflectionMethod}s.
 */
class ReflectionClass
{
    /**
     * Optional configuration parameters; accessible via {@link __get} and
     * {@link __set()}
     * @var array
     */
    protected $config = [];

    /**
     * @var ReflectionMethod[]
     */
    protected $methods = [];

    /**
     * @var string Namespace
     */
    protected $namespace;

    /**
     * @var PhpReflectionClass ReflectionClass object
     */
    protected $reflection;

    /**
     * @var string Reflection class name (needed for serialization)
     */
    protected $name;

    /**
     * Create array of dispatchable methods, each a
     * {@link ReflectionMethod}. Sets reflection object property.
     *
     * @param PhpReflectionClass $reflection
     * @param string $namespace
     * @param false|array $argv
     */
    public function __construct(PhpReflectionClass $reflection, $namespace = null, $argv = false)
    {
        $this->reflection = $reflection;
        $this->name = $reflection->getName();
        $this->setNamespace($namespace);

        foreach ($reflection->getMethods() as $method) {
            // Don't aggregate magic methods
            if ('__' == substr($method->getName(), 0, 2)) {
                continue;
            }

            if ($method->isPublic()) {
                // Get signatures and description
                $this->methods[] = new ReflectionMethod($this, $method, $this->getNamespace(), $argv);
            }
        }
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
     * Retrieve configuration parameters
     *
     * Values are retrieved by key from {@link $config}. Returns null if no
     * value found.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return null;
    }

    /**
     * Set configuration parameters
     *
     * Values are stored by $key in {@link $config}.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->config[$key] = $value;
    }

    /**
     * @return ReflectionMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get namespace for this class
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set namespace for this class
     *
     * @param string $namespace
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setNamespace($namespace)
    {
        if (empty($namespace)) {
            $this->namespace = '';
            return;
        }

        if (! is_string($namespace) || ! preg_match('/[a-z0-9_\.]+/i', $namespace)) {
            throw new Exception\InvalidArgumentException('Invalid namespace');
        }

        $this->namespace = $namespace;
    }

    /**
     * Wakeup from serialization
     *
     * Reflection needs explicit instantiation to work correctly. Re-instantiate
     * reflection object on wakeup.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->reflection = new PhpReflectionClass($this->name);
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return ['config', 'methods', 'namespace', 'name'];
    }
}
