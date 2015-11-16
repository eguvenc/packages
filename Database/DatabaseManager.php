<?php

namespace Obullo\Database;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Database Service Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
     * @param ContainerInterface $c      container
     * @param array              $params service parameters
     */
    public function __construct(Container $c, array $params)
    {
        $this->c = $c;
        $this->c['db.params'] = array_merge($params, $c['config']->load('database'));
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['db'] = function () {

            $name = $this->c['db.params']['provider']['name'];
            $params = $this->c['db.params']['provider']['params'];

            return $this->c[$name]->get(
                [
                    $params
                ]
            );
        };
    }

}