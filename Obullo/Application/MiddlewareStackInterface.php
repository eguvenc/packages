<?php

namespace Obullo\Application;

/**
 * MiddlewareStack Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface MiddlewareStackInterface
{
    /**
     * Register application middlewares
     * 
     * @param array $array middlewares
     * 
     * @return object Middleware
     */
    public function register(array $array);

    /**
     * Check given middleware is registered
     * 
     * @param string $name middleware
     * 
     * @return boolean
     */
    public function has($name);

    /**
     * Add middleware
     * 
     * @param string|array $name middleware key
     * 
     * @return object Middleware
     */
    public function add($name);

    /**
     * Returns true if middleware attached otherwise false
     * 
     * @param string $name middleware name
     * 
     * @return boolean
     */
    public function isAdded($name);

    /**
     * Returns to middleware object to inject parameters
     * 
     * @param string $name middleware
     * 
     * @return object
     */
    public function get($name);

    /**
     * Removes middleware
     * 
     * @param string|array $name middleware key
     * 
     * @return void
     */
    public function remove($name);

    /**
     * Returns to middleware queue
     * 
     * @return array
     */
    public function getQueue();

    /**
     * Returns to all middleware names
     * 
     * @return array
     */
    public function getNames();

    /**
     * Get regsitered 
     * 
     * @param string $name middleware key
     * 
     * @return string
     */
    public function getPath($name);

}