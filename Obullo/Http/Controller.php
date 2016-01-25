<?php

namespace Obullo\Http;

use Obullo\Container\ControllerInterface;
use League\Container\ImmutableContainerAwareTrait;

/**
 * Obullo Layer ( Hmvc ) Based Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Controller implements ControllerInterface
{
    use ImmutableContainerAwareTrait;
    
    /**
     * Controller instance
     * 
     * @var object
     */
    public static $instance = null;
    
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
        return $this->container->get($key);
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