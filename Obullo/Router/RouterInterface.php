<?php

namespace Obullo\Router;

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
}