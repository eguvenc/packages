<?php

namespace Obullo\Router;

use Closure;

/**
 * Router Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
     * Sets default page controller
     * 
     * @param string $page uri
     * 
     * @return object
     */
    public function defaultPage($page);

    /**
     * Set the route mapping
     * 
     * @return void
     */
    public function init();

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
     * Set the directory name : It must be lowercase otherwise sub module does not work
     *
     * @param string $directory directory
     * 
     * @return object Router
     */
    public function setDirectory($directory);

    /**
     * Sets top directory http://example.com/api/user/delete/4
     * 
     * @param string $directory sets top directory
     *
     * @return void
     */
    public function setModule($directory);

    /**
     * Get module directory
     *
     * @param string $separator directory seperator
     * 
     * @return void
     */
    public function getModule($separator = '');

    /**
     * Fetch the directory
     *
     * @return string
     */
    public function getDirectory();

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
     * @param array  $group   domain, directions and middleware name
     * @param object $closure which contains $this->attach(); methods
     * 
     * @return object
     */
    public function group(array $group, Closure $closure);

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