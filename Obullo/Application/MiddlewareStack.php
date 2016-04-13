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
class MiddlewareStack implements MiddlewareStackInterface
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
     * Registered middlewares
     * 
     * @var array
     */
    protected $registered = array();

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
     * Register application middlewares
     * 
     * @param array $array middlewares
     * 
     * @return object Middleware
     */
    public function register(array $array)
    {
        $this->registered = $array;
        return $this;
    }

    /**
     * Initialize global middlewares
     * 
     * @param array $names middlewares
     * 
     * @return void
     */
    public function init(array $names)
    {
        foreach ($names as $key) {
            $this->queueMiddleware($key);
        }
    }

    /**
     * Check given middleware is registered
     * 
     * @param string $name middleware
     * 
     * @return boolean
     */
    public function has($name)
    {
        if (array_key_exists($name, $this->registered)) {
            return true;
        }
        return false;
    }

    /**
     * Add middleware
     * 
     * @param string|array $name middleware key
     * 
     * @return object Middleware
     */
    public function add($name)
    {
        return $this->queueMiddleware($name);
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
        $this->validateMiddleware($name);

        if (isset($this->names[$name]) 
            && isset($this->queue[$this->names[$name]]) 
            && $this->getClassNameByIndex($this->names[$name]) == $name
        ) {
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
        $this->validateMiddleware($name);
        $index = $this->names[$name];
        return $this->queue[$index];
    }

    /**
     * Get class name without namespace using explode method
     * 
     * @param integer $index number
     * 
     * @return string class name without namespace
     */
    protected function getClassNameByIndex($index)
    {
        $class = get_class($this->queue[$index]);
        $exp = explode("\\", $class);
        return end($exp);
    }

    /**
     * Resolve middleware
     * 
     * @param string $name middleware key
     * 
     * @return object of middleware class
     */
    protected function queueMiddleware($name)
    {
        ++$this->count;
        $this->validateMiddleware($name);
        $Class = $this->registered[$name];
        $this->names[$name] = $this->count;
        return $this->queue[$this->count] = new $Class($this->container);  // Store middlewares
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
        $this->validateMiddleware($name);

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
     * Validate middleware
     * 
     * @param string $name middleware
     * 
     * @return void
     */
    protected function validateMiddleware($name)
    {
        if (! is_string($name)) {
            throw new RuntimeException("Middleware name must be string.");
        }
        if (! isset($this->registered[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Middleware "%s" is not registered in middlewares.php.',
                    $name
                )
            );
        }
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

    /**
     * Get regsitered path of middleware
     * 
     * @param string $name middleware key
     * 
     * @return string
     */
    public function getPath($name)
    {
        $this->validateMiddleware($name);
        return $this->registered[$name];
    }

}