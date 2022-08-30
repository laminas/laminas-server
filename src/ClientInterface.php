<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Server;

interface ClientInterface
{
    /**
     * Executes remote call
     *
     * Unified interface for calling custom remote methods.
     *
     * @param  string $method Remote call name.
     * @param  array  $params Call parameters.
     * @return mixed Remote call results.
     */
    public function call(string $method, array $params = []);
}
