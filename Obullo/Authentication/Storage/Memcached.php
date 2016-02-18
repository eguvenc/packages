<?php

namespace Obullo\Authentication\Storage;

use Interop\Container\ContainerInterface as Container;

/**
 * Memcached storage
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Memcached extends AbstractCache
{
    /**
     * Connect to cache provider
     *
     * @param object $container container
     * @param array  $params    service parameters
     * 
     * @return void
     */
    public function connect(Container $container, array $params)
    {
        $this->cache = $container->get('cacheFactory')->shared(
            [
                'driver' => 'memcached',
                'connection' => $params['cache']['provider']['connection']
            ]
        );
    }
}