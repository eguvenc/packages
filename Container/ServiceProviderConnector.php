<?php

namespace Obullo\Container;

/**
 * Service Provider Connector
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ServiceProviderConnector implements ServiceProviderInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Provider class namespace
     * 
     * @var string
     */
    protected $class;

    /**
     * Class instance
     * 
     * @var null|object
     */
    protected static $instance;

    /**
     * Connectors
     * 
     * @var array
     */
    protected static $connectors = array();

    /**
     * Returns to class instance
     * 
     * @return object
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * Set container object
     * 
     * @param ContainerInterface $c container
     *
     * @return void;
     */
    public function setContainer(ContainerInterface $c = null)
    {
        if ($this->c == null) {
            $this->c = $c;
        }
    }

    /**
     * Set current provider namespace
     * 
     * @param string $class namespace
     *
     * @return void
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Returns to container
     * 
     * @return object
     */
    protected function getContainer()
    {
        return $this->c;
    }

    /**
     * Get current provider namespace
     * 
     * @return string
     */
    protected function getClass()
    {
        return $this->class;
    }

    /**
     * Returns to provider instance.
     * 
     * Initialize just once when you call get() or factory() methods.
     * 
     * @return object
     */
    protected function connector()
    {
        $class = $this->getClass();

        if (! isset(self::$connectors[$class])) {
            self::$connectors[$class] = new $class($this->c); 
        }
        return self::$connectors[$class];
    }

    /**
     * Get connection
     *
     * @param array $params array
     *
     * @return object
     */
    public function get($params = array())
    {
        return $this->connector()->get($params);
    }

    /**
     * Create none configured new connection
     *
     * @param array $params array
     *
     * @return object
     */
    public function factory($params = array())
    {
        return $this->connector()->get($params);
    }

}