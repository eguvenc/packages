<?php

namespace Obullo\Application;

use RuntimeException;
use Interop\Container\ContainerInterface as Container;

/**
 * Middleware stack
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Middleware
{
    /**
     * Count
     * 
     * @var integer
     */
    protected $count;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Names
     * 
     * @var array
     */
    protected $names = array();

    /**
     * Middleware stack
     * 
     * @var array
     */
    protected $queue = array();

    /**
     * Constructor
     * 
     * @param Container $container object
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Add middleware
     * 
     * @param array $name   middleware
     * @param array $params parameters
     * 
     * @return void
     */
    public function add($name, $params = null)
    {
        if (is_array($name) && ! empty($name)) {
            foreach ($name as $value) {
                $this->push($value);
            }
            return;
        }
        if (is_string($name)) {
            $this->push($name, $params);
        }
    }

    /**
     * Check middleware has added
     * 
     * @param string $name middleware name
     * 
     * @return boolean
     */
    public function exists($name)
    {
        if (isset($this->names[$name])) {
            return true;
        }
        return false;
    }

    /**
     * Returns to middleware object to inject parameters
     * 
     * @param string $name middleware
     * 
     * @return object
     */
    public function get($name)
    {
        if (! isset($this->names[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Middleware "%s" is not available.',
                    $name
                )
            );
        }
        $index = $this->names[$name];
        return $this->queue[$index];
    }

    /**
     * Resolve middleware
     * 
     * @param string $name   middleware key
     * @param mixed  $params parameters
     * 
     * @return object of middleware class
     */
    protected function push($name, $params = null)
    {
        if ($this->exists($name)) {
            return;
        }
        ++$this->count;
        $Class = 'Http\Middlewares\\'.$name;
        $this->names[$name] = $this->count;

        return $this->queue[$this->count] = [
            'callable' => new $Class($this->container),
            'params' => $params
        ];
    }

    /**
     * Removes middleware
     * 
     * @param string|array $name middleware key
     * 
     * @return void
     */
    public function remove($name)
    {
        if (! isset($this->names[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Middleware "%s" is not available.',
                    $name
                )
            );
        }
        $index = $this->names[$name];
        unset($this->queue[$index], $this->names[$name]);
    }

    /**
     * Returns to middleware queue
     * 
     * @return array
     */
    public function getQueue()
    {
        return array_values($this->queue);
    }

    /**
     * Returns to all middleware names
     * 
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->names);
    }

}