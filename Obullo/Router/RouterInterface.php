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

}