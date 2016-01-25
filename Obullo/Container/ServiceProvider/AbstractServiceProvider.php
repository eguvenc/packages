<?php

namespace Obullo\Container\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider as LeagueAbstractServiceProvider;

/**
 * AbstractServiceProvider
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractServiceProvider extends LeagueAbstractServiceProvider
{
    /**
     * Load service parameters
     * 
     * @param string $name name
     * 
     * @return object|null
     */
    public function getConfiguration($name = null)
    {
        if ($name == null) {
            $class = get_class($this);
            $namespace = explode('\\', $class);
            $name = end($namespace);
            $name = strtolower($name);
        }
        $container = $this->getContainer();
        $parameters = $container->get('config')->load('providers/'.$name);

        if (is_array($parameters)) {

            $config = new Configuration($parameters);
            $container->add($name.'.params', $config->getParams());  // Inject service parameters to container

            return $config;
        }
    }
}
