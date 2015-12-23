<?php

namespace Obullo\Cli;

/**
 * Disabled http response
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NullResponse
{
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