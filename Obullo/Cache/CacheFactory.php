<?php

namespace Obullo\Cache;

use League\Container\ContainerInterface as Container;

/**
 * Cache Factory
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CacheFactory
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Connection ids
     * 
     * @var array
     */
    protected $connections = array();

    /**
     * Constructor
     * 
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get shared driver object
     * 
     * @param array $params parameters
     * 
     * @return object
     */
    public function shared($params = array())
    {
        if (empty($params['driver'])) {
            throw new RuntimeException(
                sprintf(
                    "Cache provider requires driver parameters. <pre>%s</pre>",
                    "\$cacheFactory->shared(['driver' => 'redis']);"
                )
            );
        }
        if (empty($params['connection'])) {
            $params['connection'] = 'default';
        }
        return $this->factory($params);
    }

    /**
     * Create a new cache connection
     * 
     * If you don't want to add it to config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object mongo client
     */
    public function factory($params = array())
    {
        $key = $this->getConnectionId($params);

        if (! $this->container->has($key)) {

            $driver = $params['driver'];
            unset($params['driver']);

            $this->container->share(
                $key,
                function () use ($driver, $params) {
                    return $this->createClass($driver, $params);
                }
            );
        }
        return $this->container->get($key);  // Get registered connection
    }

    /**
     * Creates cache connections
     * 
     * @param string $driver name
     * @param array  $params connect options
     * 
     * @return void
     */
    protected function createClass($driver, array $params)
    {
        $driver = strtolower($driver);
        $Class  = '\\Obullo\Cache\Handler\\'.ucfirst($driver);

        if ($driver == 'apc') {
            return new $Class;
        }
        if ($driver == 'file') {
            return new $Class($params);
        }
        $configParams = $this->container->get('config')->load('providers::'.$driver);

        return new $Class(
            $this->container->get($driver)->shared($params),
            $configParams
        );
    }

    /**
     * Creates "Unique" connection id using serialized parameters
     * 
     * @param string $string serialized parameters
     * 
     * @return integer
     */
    protected function getConnectionId($string)
    {
        $prefix = get_class($this);
        $connid = $prefix.'_'.sprintf("%u", crc32(serialize($string)));
        $this->connections[$prefix][] = $connid;
        return $connid;
    }
}
