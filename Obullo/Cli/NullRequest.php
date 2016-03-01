<?php

namespace Obullo\Cli;

use Obullo\Cli\Uri;

/**
 * Disabled http request
 * 
 * @copyright 2009-2016 Obullo
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
    public function getServerParams() { return array(); }
    public function getCookieParams() { return array(); }
    public function withCookieParams(array $cookies){ return $this; }
    public function getQueryParams(){ return array(); }
    public function withQueryParams(array $query) { return $this; }
    public function getUploadedFiles() { return array(); }
    public function withUploadedFiles(array $uploadedFiles) { return $this; }
    public function getParsedBody() { return null; }
    public function withParsedBody($data) { return $this; }
    public function getAttributes() { return array(); }
    public function getAttribute($name, $default = null) { return null; }
    public function withAttribute($name, $value) { return $this; }
    public function withoutAttribute($name) { return $this; }
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