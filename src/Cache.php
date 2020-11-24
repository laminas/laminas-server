<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server;

use ErrorException;
use Laminas\Stdlib\ErrorHandler;

use function array_keys;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function is_readable;
use function is_writable;
use function serialize;
use function unlink;
use function unserialize;

use const E_NOTICE;

class Cache
{
    /** @var string[] */
    protected static $skipMethods = [];

    /**
     * Cache a file containing the dispatch list.
     *
     * Serializes the server definition stores the information
     * in $filename.
     *
     * Returns false on any error (typically, inability to write to file), true
     * on success.
     *
     * @throws ErrorException
     */
    public static function save(string $filename, ServerInterface $server): bool
    {
        if (! file_exists($filename) && ! is_writable(dirname($filename))) {
            return false;
        }

        $methods = self::createDefinition($server->getFunctions());

        ErrorHandler::start();
        $test = file_put_contents($filename, serialize($methods));
        ErrorHandler::stop();
        if (0 === $test) {
            return false;
        }

        return true;
    }

    /**
     * Load server definition from a file
     *
     * Unserializes a stored server definition from $filename. Returns false if
     * it fails in any way, true on success.
     *
     * Useful to prevent needing to build the server definition on each
     * request. Sample usage:
     *
     * <code>
     * if (!Laminas\Server\Cache::get($filename, $server)) {
     *     require_once 'Some/Service/ServiceClass.php';
     *     require_once 'Another/Service/ServiceClass.php';
     *
     *     // Attach Some\Service\ServiceClass with namespace 'some'
     *     $server->attach('Some\Service\ServiceClass', 'some');
     *
     *     // Attach Another\Service\ServiceClass with namespace 'another'
     *     $server->attach('Another\Service\ServiceClass', 'another');
     *
     *     Laminas\Server\Cache::save($filename, $server);
     * }
     *
     * $response = $server->handle();
     * echo $response;
     * </code>
     *
     * @throws ErrorException
     */
    public static function get(string $filename, ServerInterface $server): bool
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            return false;
        }

        ErrorHandler::start();
        $dispatch = file_get_contents($filename);
        ErrorHandler::stop();
        if (false === $dispatch) {
            return false;
        }

        ErrorHandler::start(E_NOTICE);
        $dispatchArray = unserialize($dispatch);
        ErrorHandler::stop();
        if (false === $dispatchArray) {
            return false;
        }

        $server->loadFunctions($dispatchArray);

        return true;
    }

    public static function delete(string $filename): bool
    {
        if (file_exists($filename)) {
            unlink($filename);
            return true;
        }

        return false;
    }

    /**
     * @param array|Definition $methods
     * @return array|Definition
     */
    private static function createDefinition($methods)
    {
        if ($methods instanceof Definition) {
            return self::createDefinitionFromMethodsDefinition($methods);
        }

        return self::createDefinitionFromMethodsArray($methods);
    }

    private static function createDefinitionFromMethodsDefinition(Definition $methods): Definition
    {
        $definition = new Definition();
        foreach ($methods as $method) {
            if (in_array($method->getName(), static::$skipMethods, true)) {
                continue;
            }
            $definition->addMethod($method);
        }
        return $definition;
    }

    private static function createDefinitionFromMethodsArray(array $methods): array
    {
        foreach (array_keys($methods) as $methodName) {
            if (in_array($methodName, static::$skipMethods, true)) {
                unset($methods[$methodName]);
            }
        }
        return $methods;
    }
}
