<?php

namespace Obullo\Cli;

use Obullo\Cli\Uri;

/**
 * Disabled http request
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NullRequest
{
    /**
     * Uri
     * 
     * @var object
     */
    protected $uri;

    /**
     * Returns to cli uri
     * 
     * @return object
     */
    public function getUri()
    {
        if ($this->uri == null) {  // Lazy loader
            $this->uri = new Uri; 
        }
        return $this->uri;
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