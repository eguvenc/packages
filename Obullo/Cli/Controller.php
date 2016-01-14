<?php

namespace Obullo\Cli;

use Obullo\Cli\ControllerInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Obullo Layer ( Hmvc ) Based Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Controller implements ControllerInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c; 
    
    /**
     * Set container
     * 
     * @param Container $container container object
     * 
     * @return void
     */
    public function __setContainer(Container $container = null)
    {
        if ($this->c == null) {
            $this->c = &$container;
        }
    }

    /**
     * Container proxy
     * 
     * @param string $key key
     * 
     * @return object Controller
     */
    public function __get($key)
    {
        return $this->c[$key];
    }

    /**
     * We prevent to set none object variables
     *
     * Forexample this is not allowed $this->user_variable = 'example'.
     * 
     * @param string $key string
     * @param string $val mixed
     *
     * @return void 
     */
    public function __set($key, $val)  // Custom variables is not allowed !!! 
    {
        if (is_object($val)) {
            $this->{$key} = $val; // WARNING : Store only object types otherwise container params
                                  // variables come in here.
        }
    }
}