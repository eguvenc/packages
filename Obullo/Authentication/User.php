<?php

namespace Obullo\Authentication;

use Interop\Container\ContainerInterface as Container;

/**
 * Auth controller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class User
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Class loader
     * 
     * @param string $class name
     * 
     * @return object | null
     */
    public function __get($class)
    {
        return $this->container->get('auth.'.strtolower($class)); // Call services: $this->user->login, $this->user->identity, $this->user->model ..
    }
}