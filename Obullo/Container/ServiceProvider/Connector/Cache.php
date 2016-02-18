<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use Obullo\Config\ConfigInterface as Config;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Cache Service Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Cache extends AbstractServiceProvider
{
    /**
     * Config
     * 
     * @var object
     */
    protected $config;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param object $container container
     * @param object $config    config
     */
    public function __construct(Container $container, Config $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Register connections
     * 
     * @return void
     */
    public function register()
    {
        return;
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
        if (empty($params['driver']) || empty($params['connection'])) {
            throw new RuntimeException(
                sprintf(
                    "Cache provider requires driver and connection parameters. <pre>%s</pre>",
                    "\$container->get('cacheFactory')->shared(['driver' => 'redis', 'connection' => 'default']);"
                )
            );
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
            $options = (! empty($params['options'])) ? $params['options'] : array();
            return new $Class($options);
        }
        $configParams = $this->config->load('providers::'.$driver);

        return new $Class(
            $this->container->get($driver)->shared($params),
            $configParams
        );
    }
}
