<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server;

use ReflectionClass;
use ReflectionException;

use function call_user_func_array;
use function is_object;

abstract class AbstractServer implements ServerInterface
{
    /** @var bool */
    protected $overwriteExistingMethods = false;

    /** @var Definition */
    protected $table;

    public function __construct()
    {
        $this->table = new Definition();
        $this->table->setOverwriteExistingMethods($this->overwriteExistingMethods);
    }

    public function getFunctions(): Definition
    {
        return $this->table;
    }

    /**
     * Build callback for method signature
     *
     * @deprecated Since 2.7.0; method will have private visibility starting in 3.0.
     */
    // phpcs:disable
    protected function _buildCallback(Reflection\AbstractFunction $reflection): Method\Callback
    {
    // phpcs:enable
        $callback = new Method\Callback();
        if ($reflection instanceof Reflection\ReflectionMethod) {
            $callback->setType($reflection->isStatic() ? 'static' : 'instance')
                     ->setClass($reflection->getDeclaringClass()->getName())
                     ->setMethod($reflection->getName());
        } elseif ($reflection instanceof Reflection\ReflectionFunction) {
            $callback->setType('function')
                     ->setFunction($reflection->getName());
        }
        return $callback;
    }

    /**
     * Build a method signature
     *
     * @deprecated Since 2.7.0; method will be renamed to remove underscore
     *     prefix in 3.0.
     *
     * @param  Reflection\AbstractFunction $reflection
     * @param  null|string|object $class
     * @return Method\Definition
     * @throws Exception\RuntimeException on duplicate entry
     */
    // phpcs:disable
    protected function _buildSignature(Reflection\AbstractFunction $reflection, $class = null): Method\Definition
    {
    // phpcs:enable
        $ns     = $reflection->getNamespace();
        $name   = $reflection->getName();
        $method = empty($ns) ? $name : $ns . '.' . $name;

        if (! $this->overwriteExistingMethods && $this->table->hasMethod($method)) {
            throw new Exception\RuntimeException('Duplicate method registered: ' . $method);
        }

        $definition = new Method\Definition();
        $definition->setName($method)
                   ->setCallback($this->_buildCallback($reflection))
                   ->setMethodHelp($reflection->getDescription())
                   ->setInvokeArguments($reflection->getInvokeArguments());

        foreach ($reflection->getPrototypes() as $proto) {
            $prototype = new Method\Prototype();
            $prototype->setReturnType($this->_fixType($proto->getReturnType()));
            foreach ($proto->getParameters() as $parameter) {
                $param = new Method\Parameter([
                    'type'     => $this->_fixType($parameter->getType()),
                    'name'     => $parameter->getName(),
                    'optional' => $parameter->isOptional(),
                ]);
                if ($parameter->isDefaultValueAvailable()) {
                    $param->setDefaultValue($parameter->getDefaultValue());
                }
                $prototype->addParameter($param);
            }
            $definition->addPrototype($prototype);
        }
        if (is_object($class)) {
            $definition->setObject($class);
        }
        $this->table->addMethod($definition);
        return $definition;
    }

    /**
     * Dispatch method
     *
     * @deprecated Since 2.7.0; method will be renamed to remove underscore
     *     prefix in 3.0.
     *
     * @return mixed
     * @throws ReflectionException
     */
    // phpcs:disable
    protected function _dispatch(Method\Definition $invokable, array $params)
    {
    // phpcs:enable
        $callback = $invokable->getCallback();
        $type     = $callback->getType();

        if ('function' === $type) {
            $function = $callback->getFunction();
            // phpcs:disable
            return call_user_func_array($function, $params);
            // phpcs:enable
        }

        $class  = $callback->getClass();
        $method = $callback->getMethod();

        if ('static' === $type) {
            // phpcs:disable
            return call_user_func_array([$class, $method], $params);
            // phpcs:enable
        }

        $object = $invokable->getObject();
        if (! is_object($object)) {
            $invokeArgs = $invokable->getInvokeArguments();
            if (! empty($invokeArgs)) {
                $reflection = new ReflectionClass($class);
                $object     = $reflection->newInstanceArgs($invokeArgs);
            } else {
                $object = new $class();
            }
        }
        // phpcs:disable
        return call_user_func_array([$object, $method], $params);
        // phpcs:enable
    }

    // phpcs:disable
    /**
     * Map PHP type to protocol type
     *
     * @deprecated Since 2.7.0; method will be renamed to remove underscore
     *     prefix in 3.0.
     */
    abstract protected function _fixType(string $type): string;
    // phpcs:enable
}
