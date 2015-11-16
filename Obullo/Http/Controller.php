<?php

namespace Obullo\Http;

use Obullo\Http\ControllerInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Obullo Layer ( Hmvc ) Based Controller.
 * 
 * @copyright 2009-2015 Obullo
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
     * Controller instance
     * 
     * @var object
     */
    public static $instance = null;
    
    /**
     * Set container
     * 
     * @param Container $c container object
     * 
     * @return void
     */
    public function __setContainer(Container $c = null)
    {
        if ($this->c == null) {
            $this->c = &$c;
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
        if (self::$instance == null || in_array($key, ['request', 'router', 'view'])) {  // Create new layer for each core classes ( Otherwise Layer does not work )
            self::$instance = &$this;
        }
        return $this->c[$key];
    }

    /**
     * We prevent to set none object variables
     *
     * Forexample in controller this is not allowed $this->user_variable = 'hello'.
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