<?php

namespace Obullo\Cli;

/**
 * Disabled http middleware class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NullMiddleware
{
    /**
     * Returns to middleware queue
     * 
     * @return array
     */
    public function getQueue()
    {
        return array();
    }

    /**
     * Magic null
     * 
     * @param string $method name
     * @param array  $args   arguments
     * 
     * @return null
     */
    public function __call($method, $args)
    {
        return $method = $args = null;
    }
}