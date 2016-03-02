<?php

namespace Obullo\Database;

/**
 * Model Class ( Default Model )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Model
{
    /**
     * Returns to service object
     * 
     * @param string $key 
     * 
     * @return object
     */
    public function __get($key)
    {
        if ((PHP_SAPI === 'cli' || defined('STDIN'))) {
            $object = \Obullo\Cli\Controller::$instance->container->get($key);
        } else {
            $object = \Obullo\Http\Controller::$instance->container->get($key);
        }
        if (is_object($object)) {
            return $object;
        }
        return;
    }
}