<?php

namespace Obullo\Router;

use Closure;

/**
 * Router Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouterInterface
{
    /**
     * Configure router
     * 
     * @param array $params config params
     * 
     * @return void
     */
    public function configure(array $params);

    /**
     * Set the route mapping
     * 
     * @return void
     */
    public function init();

    /**
     * Sets default page controller
     * 
     * @param string $page uri
     * 
     * @return object
     */
    public function defaultPage($page);

    /**
     * Set the class name
     * 
     * @param string $class classname segment 1
     *
     * @return object Router
     */
    public function setClass($class);

    /**
     * Set current method
     * 
     * @param string $method name
     *
     * @return object Router
     */
    public function setMethod($method);

    /**
     * Set the folder name : It must be lowercase otherwise sub module does not work
     *
     * @param string $folder name
     * 
     * @return object Router
     */
    public function setFolder($folder);

    /**
     * Sets top folder http://example.com/api/user/delete/4
     * 
     * @param string $folder sets top folder
     *
     * @return void
     */
    public function setPrimaryFolder($folder);

    /**
     * Get module directory
     *
     * @param string $separator directory seperator
     * 
     * @return void
     */
    public function getPrimaryFolder($separator = '');

    /**
     * Fetch the directory
     *
     * @return string
     */
    public function getFolder();

    /**
     * Fetch the current routed class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Returns to current method
     * 
     * @return string
     */
    public function getMethod();

    /**
     * Returns php namespace of the current route
     * 
     * @return string
     */
    public function getNamespace();

    /**
     * Create grouped routes
     * 
     * @param string $uri     match route
     * @param object $closure which contains $this->attach(); methods
     * @param array  $group   domain, directions and middleware name
     * 
     * @return object
     */
    public function group($uri, $closure = null, $group = array());

    /**
     * Creates http GET based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function get($match, $rewrite = null, $closure = null);

    /**
     * Creates http POST based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function post($match, $rewrite = null, $closure = null);

    /**
     * Creates http PUT based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function put($match, $rewrite = null, $closure = null);

    /**
     * Creates http DELETE based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function delete($match, $rewrite = null, $closure = null);

    /**
     * Creates multiple http route
     * 
     * @param string $methods http methods
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function match($methods, $match, $rewrite = null, $closure = null);

    /**
     * Returns to attachment
     * 
     * @return object
     */
    public function getAttach();

    /**
     * Returns to domain object
     * 
     * @return object
     */
    public function getDomain();

    /**
     * Returns to route object
     * 
     * @return route
     */
    public function getRoute();

    /**
     * Clear some parameters for layers.
     * 
     * @return void
     */
    public function clear();

}