<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server;

use Laminas\Server\Reflection\Exception\InvalidArgumentException;
use Laminas\Server\Reflection\ReflectionClass;
use Laminas\Server\Reflection\ReflectionFunction;
use ReflectionException;
use ReflectionObject;

class Reflection
{
    /**
     * Perform class reflection to create dispatch signatures
     *
     * Creates a {@link \Laminas\Server\Reflection\ClassReflection} object for the class or
     * object provided.
     *
     * If extra arguments should be passed to dispatchable methods, these may
     * be provided as an array to $argv.
     *
     * @param string|object $class Class name or object
     * @param null|array $argv Optional arguments to be used during the method call
     * @param null|string $namespace Optional namespace with which to prefix the
     * method name (used for the signature key). Primarily to avoid collisions,
     * also for XmlRpc namespacing
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public static function reflectClass($class, ?array $argv = null, ?string $namespace = null): ReflectionClass
    {
        if (is_object($class)) {
            $reflection = new ReflectionObject($class);
        } elseif (is_string($class) && class_exists($class)) {
            $reflection = new \ReflectionClass($class);
        } else {
            throw new Reflection\Exception\InvalidArgumentException('Invalid class or object passed to attachClass()');
        }

        return new ReflectionClass($reflection, $namespace, $argv);
    }

    /**
     * Perform function reflection to create dispatch signatures
     *
     * Creates dispatch prototypes for a function. It returns a
     * {@link Laminas\Server\Reflection\FunctionReflection} object.
     *
     * If extra arguments should be passed to the dispatchable function, these
     * may be provided as an array to $argv.
     *
     * @param string|callable $function Function name
     * @param  null|array $argv Optional arguments to be used during the method call
     * @param null|string $namespace Optional namespace with which to prefix the
     * function name (used for the signature key). Primarily to avoid
     * collisions, also for XmlRpc namespacing
     * @return ReflectionFunction
     * @throws InvalidArgumentException|ReflectionException
     */
    public static function reflectFunction(
        $function,
        ?array $argv = null,
        ?string $namespace = null
    ): ReflectionFunction {
        if (! is_string($function) || ! function_exists($function)) {
            throw new Reflection\Exception\InvalidArgumentException(sprintf(
                'Invalid function "%s" passed to reflectFunction',
                $function
            ));
        }

        return new ReflectionFunction(new \ReflectionFunction($function), $namespace, $argv);
    }
}
