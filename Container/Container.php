<?php

namespace Obullo\Container;

use Closure;
use ArrayAccess;
use RuntimeException;
use InvalidArgumentException;

/**
 * Obullo Lightweight Php DI
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Container implements ContainerInterface, ArrayAccess
{
    protected $raw = array();
    protected $frozen = array();
    protected $values = array();
    protected $keys = array();
    protected $unset = array();      // Stores classes we want to remove
    protected $loader = array();     // Service loader object
    protected $services = array();   // Service array
    protected $providers = array();  // Service array

    /**
     * Register service classes if required
     * 
     * @param array $loader service loader object
     * 
     * @return void
     */
    public function __construct($loader = null)
    {
        $this->loader = $loader;
    }

    /**
     * Register your services
     * 
     * @param array $services service array
     * 
     * @return void
     */
    public function service(array $services)
    {
        $this->services = $services;
    }

    /**
     * Register your service providers
     * 
     * @param array $providers service providers
     * 
     * @return void
     */
    public function provider(array $providers)
    {
        foreach ((array)$providers as $name => $namespace) {
            $this->providers[$name] = $namespace;
        }
    }

    /**
     * Resolve service providers
     *
     * @param string $cid class name
     * 
     * @return object provider
     */
    public function loadServiceProvider($cid)
    {
        $cid = strtolower($cid);
        $connector = ServiceProviderConnector::getInstance();
        $connector->setContainer($this);
        $connector->setClass($this->providers[$cid]);
        return $connector;
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $cid The unique identifier for the parameter or object
     *
     * @return Boolean
     */
    public function has($cid) 
    {
        return $this->offsetExists($cid);   // Is it component ?
    }

    /**
     * Checks package is old / loaded before
     * 
     * @param string $cid package id
     * 
     * @return boolean
     */
    public function active($cid)
    {
        return isset($this->frozen[$cid]);
    }    

    /**
     * Sets a parameter or an object.
     *
     * Objects must be defined as Closures.
     *
     * Allowing any PHP callable leads to difficult to debug problems
     * as function names (strings) are callable (creating a function with
     * the same name as an existing parameter would break your container).
     *
     * @param string $cid   The unique identifier for the parameter or object
     * @param mixed  $value The value of the parameter or a closure to define an object
     * 
     * @return void
     */
    public function offsetSet($cid, $value)
    {   
        if (isset($this->frozen[$cid])) {
            return;
        }
        $this->values[$cid] = $value;
        $this->keys[$cid] = true;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $cid    The unique identifier for the parameter or object
     * @param array  $params Parameters
     * 
     * @return mixed The value of the parameter or an object
     *
     * @throws InvalidArgumentException if the identifier is not defined
     */
    public function offsetGet($cid, $params = array())
    {
        if (isset($this->providers[$cid])) {  // First resolve service providers
            return $this->loadServiceProvider($cid);
        }   
        if (! isset($this->values[$cid])) {     // If does not exist in container we load it directly.
            return $this->load($cid);           // Load services or components.
        }
        if (isset($this->raw[$cid])             // Returns to instance of class or raw closure.
            || ! is_object($this->values[$cid])
            || ! method_exists($this->values[$cid], '__invoke')
        ) {
            return $this->values[$cid];
        }
        $this->frozen[$cid] = true;
        $this->raw[$cid] = $this->values[$cid];
        return $this->values[$cid] = $this->closure($this->values[$cid], $params);
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $cid The unique identifier for the parameter or object
     *
     * @return Boolean
     */
    public function offsetExists($cid)
    {
        return isset($this->keys[$cid]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $cid The unique identifier for the parameter or object
     *
     * @return void
     */
    public function offsetUnset($cid)
    {
        if (isset($this->keys[$cid])) {
            if (is_object($this->values[$cid])) {
                unset($this->protected[$this->values[$cid]]);
            }
            unset($this->values[$cid], $this->frozen[$cid], $this->raw[$cid], $this->keys[$cid]);
        }
        $this->unset[$cid] = true;
    }

    /**
     * Class and Service loader
     *
     * @param string $classString class command
     * @param array  $params      closure params
     * 
     * @return void
     */
    public function load($classString, $params = array())
    {
        $class = trim($classString);
        $cid = strtolower($class);

        $isService = false;
        if (is_object($this->loader) && count($this->services) > 0) {

            $this->loader->setContainer($this);
            if ($this->loader->resolveServices($class)) {
                $isService = true;
            }
        }
        if (! $this->has($cid) && ! $isService) {
            throw new RuntimeException(
                sprintf(
                    'The class "%s" is not available in container. Please register it as a component, service or service provider.',
                    $cid
                )
            );
        }
        return $this->offsetGet($cid, $params);
    }

    /**
     * Alias of array access $c['key'];
     * 
     * @param string $cid class id
     * 
     * @return object
     */
    public function get($cid)
    {
        return $this[$cid];
    }

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @param string $cid The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or the closure defining an object
     */
    public function raw($cid)
    {
        if (! isset($this->keys[$cid])) {
            return null;
        }
        if (isset($this->raw[$cid])) {
            return $this->raw[$cid];
        }
        return $this->values[$cid];
    }

    /**
     * Returns to service array
     * 
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
    * Closure helper: run callable function with params
    * 
    * @param object $func   callable
    * @param array  $params parameters
    * 
    * @return object closure
    */
    protected function closure(Closure $func, $params = array())
    {
        if (count($params) > 0) {
            return $func($params);
        }
        return $func();
    }

    /**
     * Returns all defined value names.
     *
     * @return array An array of value names
     */
    public function keys()
    {
        return array_keys($this->values);
    }
    
    /**
     * Magic method var_dump($c) wrapper ( for PHP 5.6.0 and newer versions )
     * 
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'classes' => $this->keys()
        ];
    }

}
