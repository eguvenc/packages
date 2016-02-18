<?php

namespace Obullo\Database;

use Obullo\Http\Controller;

/**
 * Model Class ( Default Model )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Model
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Container
     * 
     * @return object
     */
    public function getContainer()
    {
        return Controller::$instance->getContainer();
    }

    /**
     * Returns to service object
     * 
     * @param string $key 
     * 
     * @return object
     */
    public function __get($key)
    {
        $return = $this->getContainer()->{$key};

        if (is_object($return)) {
            return $return;
        }
        return;
    }
}