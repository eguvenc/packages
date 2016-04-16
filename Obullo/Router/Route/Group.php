<?php

namespace Obullo\Router\Route;

use Closure;
use Obullo\Router\Route\Attach;
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
     * Group match
     * 
     * @var boolean
     */
    protected $match = false;

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
     * @param string $uri     route
     * @param object $closure which contains $this->attach(); methods
     * @param array  $options domain, directions and middleware name
     * 
     * @return bool|void
     */
    public function addGroup($uri, Closure $closure, $options = array())
    {
        if (! empty($uri) && ! $this->uriMatch($uri)) {
            return;
        }
        if (! $this->domain->match()) {  // When groups run, if domain not match with regex don't continue.
            return false;                // Forexample we define a sub domain but group 
                                         // domain doesn't match we need to stop the propagation.
        }
        $this->options = $options;
        $closure = Closure::bind(
            $closure,
            $this->router,
            get_class($this->router)
        );
        $subname = $this->getSubDomainValue();
        $closure(['subname' => $subname]);

        $this->match = true;
    }

    /**
     * Attach middlewars
     * 
     * @param string $middleware middleware
     * @param mixed  $params     middleware parameters
     *
     * @return void
     */
    public function add($middleware, $params = null)
    {
        if (! is_string($middleware)) {
            trigger_error("Middleware value must be string.");
        }
        $this->options['middleware'][] = array('name' => $middleware, 'params' => (array)$params);
        return $this;
    }

    /**
     * Attach route to middleware
     * 
     * @param string $route middleware route
     * 
     * @return object
     */
    public function attach($route = "*")
    {
        if ($this->match) {
            /**
             * We have to use setGroup() functions otherwise
             * single routes override attach object properties, so using set
             * methods we keep the dynmaic values of group object.
             */
            $this->router->getAttach()->setDomain($this->domain)->setGroup($this)->toGroup($route);
        }
        return $this;
    }

    /**
     * Check uri match 
     * 
     * @param string $match match url or regex
     * 
     * @return boolean
     */
    protected function uriMatch($match)
    {
        $exp = explode('/', trim($this->uri->getPath(), "/"));
        return in_array(trim($match, "/"), $exp, true);
    }

    /**
     * Get subdomain value
     * 
     * @return string
     */
    protected function getSubDomainValue()
    {
        $matches    = $this->domain->getMatches();
        $domainName = $this->domain->getName(); // (empty($options['domain'])) ? null : $options['domain'];

        $sub = false;
        if (isset($matches[$domainName])) {
            $sub = $this->domain->getSubName($matches[$domainName]);
        }
        return $sub;
    }

    /**
     * Reset group variables
     * 
     * @return void
     */
    public function end()
    {
        $this->match = false;
        $this->options = array();
        $this->domain->setName($this->domain->getImmutable());  // Restore domain
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