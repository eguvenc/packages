<?php

namespace Obullo\Router\Route;

use Closure;
use Psr\Http\Message\UriInterface as Uri;
use Obullo\Router\RouterInterface as Router;

/**
 * Group functionality
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Group
{
    /**
     * Router
     * 
     * @var object
     */
    protected $router;

    /**
     * Domain
     * 
     * @var object
     */
    protected $domain;

    /**
     * Route group data
     * 
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     * 
     * @param Router $router router
     * @param Uri    $uri    uri
     */
    public function __construct(Router $router, Uri $uri)
    {
        $this->router = $router;
        $this->uri    = $uri;
        $this->domain = $router->getDomain();
    }

    /**
     * Create grouped routes
     * 
     * @param array  $options domain, directions and middleware name ..
     * @param object $closure which contains $this->attach(); methods
     * 
     * @return object
     */
    public function add(array $options, Closure $closure)
    {
        if (isset($options['match']) && ! $this->match($options)) {
            return $this;
        }
        if (! $this->domain->match($options)) {  // When groups run, if domain not match with regex don't continue.
            return $this;                        // Forexample we define a sub domain but group domain does not match
        }                                        // so we need to stop the propagation.
        $this->options = $options;

        $closure = Closure::bind(
            $closure,
            $this->router,
            get_class($this->router)
        );
        $subDomain = $this->getSubDomainValue($options);
        $closure($subDomain);

        $this->reset();  // Reset group variable after foreach group definition
    }

    /**
     * Detect class namespace
     * 
     * @param array $options array
     * 
     * @return bool
     */
    protected function match(array $options)
    {
        if ($this->hasMatch($options['match'])) {
            return true;
        }
        return false;
    }

    /**
     * Does group has match ? ( ['match' => $regex] )
     *
     * @param string $match class namespace
     * 
     * @return bool
     */
    protected function hasMatch($match)
    {
        $uri = ltrim($this->uri->getPath(), '/');

        if ($this->hasNaturalMatch($match, $uri)) {
            return true;
        }
        if ($this->router->hasRegexMatch($match, $uri)) {
            return true;
        }
        return false;

    }

    /**
     * Before regex check natural uri match 
     * 
     * @param string $match match url or regex
     * @param string $uri   uri string
     * 
     * @return boolean
     */
    protected function hasNaturalMatch($match, $uri)
    {
        $exp = explode('/', $uri);
        $uriStr = '';
        $keyValues = array_keys(explode('/', $match));
        foreach ($keyValues as $k) {
            if (isset($exp[$k])) {
                $uriStr.= strtolower($exp[$k]).'/';
            }
        }
        if ($match == rtrim($uriStr, '/')) {
            return true;
        }
        return false;
    }

    /**
     * Get subdomain value
     *
     * @param array $options group data
     * 
     * @return string
     */
    protected function getSubDomainValue(array $options)
    {
        $matches = $this->domain->getMatches();
        $domainName = (isset($options['domain'])) ? $options['domain'] : null;

        $sub = false;
        if (isset($matches[$domainName])) {
            $sub = strstr($matches[$domainName], '.', true);
        }
        return $sub;
    }

    /**
     * Reset group options
     * 
     * @return void
     */
    public function reset()
    {
        $this->options = array('name' => 'UNNAMED', 'domain' => null);
    }

    /**
     * Returns to current group value
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

}