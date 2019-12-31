<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server;

use Laminas\Stdlib\ErrorHandler;

/**
 * \Laminas\Server\Cache: cache server definitions
 *
 * @category   Laminas
 * @package    Laminas_Server
 */
class Cache
{
    /**
     * @var array Methods to skip when caching server
     */
    protected static $skipMethods = array();

    /**
     * Cache a file containing the dispatch list.
     *
     * Serializes the server definition stores the information
     * in $filename.
     *
     * Returns false on any error (typically, inability to write to file), true
     * on success.
     *
     * @param  string $filename
     * @param  \Laminas\Server\Server $server
     * @return bool
     */
    public static function save($filename, Server $server)
    {
        if (!is_string($filename)
            || (!file_exists($filename) && !is_writable(dirname($filename))))
        {
            return false;
        }

        $methods = $server->getFunctions();

        if ($methods instanceof Definition) {
            $definition = new Definition();
            foreach ($methods as $method) {
                if (in_array($method->getName(), self::$skipMethods)) {
                    continue;
                }
                $definition->addMethod($method);
            }
            $methods = $definition;
        }

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
     * @param  string $filename
     * @param  \Laminas\Server\Server $server
     * @return bool
     */
    public static function get($filename, Server $server)
    {
        if (!is_string($filename)
            || !file_exists($filename)
            || !is_readable($filename))
        {
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

    /**
     * Remove a cache file
     *
     * @param  string $filename
     * @return boolean
     */
    public static function delete($filename)
    {
        if (is_string($filename) && file_exists($filename)) {
            unlink($filename);
            return true;
        }

        return false;
    }
}
