<?php

namespace Obullo\Database;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Database Service Manager
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class DatabaseManager implements ServiceInterface
{
    /**
     * Container class
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     * 
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Set service parameters
     * 
     * @param array $params service configuration
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->c['db.params'] = array_merge(
            $params,
            $this->c['config']->load('database')
        );
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['db'] = function () {

            $name   = $this->c['db.params']['provider']['name'];
            $params = $this->c['db.params']['provider']['params'];

            return $this->c[$name]->get(
                [
                    $params
                ]
            );
        };
    }

}