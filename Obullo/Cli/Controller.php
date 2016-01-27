<?php

namespace Obullo\Cli;

use Obullo\Http\ImmutableControllerInterface;
use League\Container\ImmutableContainerAwareTrait;

/**
 * Obullo Layer ( Hmvc ) Based Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Controller implements ImmutableControllerInterface
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
        if (self::$instance == null) {
            self::$instance = &$this;
        }
        return $this->getContainer()->get($key);
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