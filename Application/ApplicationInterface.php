<?php

namespace Obullo\Application;

use Closure;
use Exception;

/**
 * Interface Application
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ApplicationInterface
{
    /**
     * Sets application exception errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function error(Closure $closure);

    /**
     * Sets application fatal errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function fatal(Closure $closure);

    /**
     * Is Cli ?
     *
     * Test to see if a request was made from the command line.
     *
     * @return bool
     */
    public function isCli();

    /**
     * Returns to detected environment
     * 
     * @return string
     */
    public function env();

    /**
     * Registers a service provider.
     *
     * @param array $providers provider name and namespace array
     *
     * @return object
     */
    public function provider(array $providers);

    /**
     * Register services
     * 
     * @param array $services services
     * 
     * @return object
     */
    public function service(array $services);

    /**
     * Register components & resolve dependencies
     *
     * @param array $namespaces component class name & namespaces
     * 
     * @return void
     */
    public function component(array $namespaces);

    /**
     * Creates dependency
     * 
     * @param array $deps dependencies
     * 
     * @return object
     */
    public function dependency(array $deps);

    /**
     * Returns current version of Obullo
     * 
     * @return string
     */
    public function version();
}