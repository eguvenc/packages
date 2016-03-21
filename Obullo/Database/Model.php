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
        $object = $this->getContainer()->get($key);

        if (is_object($object)) {
            return $object;
        }
        return;
    }

    /**
     * Returns to container
     * 
     * @return object
     */
    public function getContainer()
    {
        if (defined('STDIN') && ! empty($_SERVER['argv'][0]) && $_SERVER['argv'][0] == 'task') {
            return \Obullo\Cli\Controller::$instance->container->get($key);
        } else {
            return \Obullo\Http\Controller::$instance->container->get($key);
        }
    }

}