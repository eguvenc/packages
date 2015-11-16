<?php

namespace Obullo\Log;

/**
 * Abstract Logger
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractLogger
{
    /**
     * Track data for handlers and writers
     * 
     * @var array
     */
    protected $track = array();

    /**
     * On / Off logging
     * 
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Namespaces of defined filters
     * 
     * @var array
     */
    protected $filterNames = array();

    /**
     * Register log handlers
     * 
     * @var array
     */
    protected $registeredHandlers = array();
    
    /**
     * Map native PHP errors to priority
     *
     * @var array
     */
    protected static $errorPriorities = null;

    /**
     * Enable logging
     * 
     * @return object Logger
     */
    public function enable()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Disable logging
     * 
     * @return object Logger
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Returns to boolean whether to know logging 
     * is enabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Register filter alias
     * 
     * @param string $name      name of filter
     * @param string $namespace filename and path of filter
     *
     * @return object
     */
    public function registerFilter($name, $namespace)
    {
        $this->filterNames[$name] = ltrim($namespace, '\\');
        return $this;
    }

    /**
     * Register handler
     * 
     * @param string $priority global priority
     * @param string $name     handler name which is defined in constants
     * 
     * @return object
     */
    public function registerHandler($priority, $name)
    {
        $this->registeredHandlers[$name] = array('priority' => $priority);
        $this->track[] = array('name' => $name);
        return $this;
    }

    /**
     * Returns all or selected priorities
     * 
     * @return array
     */
    public function getPriorities()
    {
        return $this->params['priorities'];
    }

    /**
     * Returns to all error priorities
     * 
     * @return array
     */
    public function getErrorPriorities()
    {
        if (static::$errorPriorities != null) {
            return static::$errorPriorities;
        }
        $map = $this->getPriorities();
        return static::$errorPriorities = array(
                E_NOTICE            => $map['notice'],
                E_USER_NOTICE       => $map['notice'],
                E_WARNING           => $map['warning'],
                E_CORE_WARNING      => $map['warning'],
                E_USER_WARNING      => $map['warning'],
                E_ERROR             => $map['error'],
                E_USER_ERROR        => $map['error'],
                E_CORE_ERROR        => $map['error'],
                E_RECOVERABLE_ERROR => $map['error'],
                E_STRICT            => $map['debug'],
                E_DEPRECATED        => $map['debug'],
                E_USER_DEPRECATED   => $map['debug'],
            );
    }

    /**
     * Load defined log handler
     * 
     * @param string $name defined log handler name
     * 
     * @return object
     */
    abstract public function load($name);

    /**
     * Change channel
     * 
     * @param string $channel add a channel
     * 
     * @return object
     */
    abstract public function channel($channel);

    /**
     * Reserve your filter to valid log handler
     * 
     * @param string $name   filter name
     * @param array  $params data
     * 
     * @return object
     */
    abstract public function filter($name, $params = array());

    /**
     * Push to another handler
     * 
     * @return object
     */
    abstract public function push();
    
    /**
     * Add writer
     * 
     * @param string $name handler key
     *
     * @return object
     */
    abstract public function setWriter($name);

    /**
     * Returns to primary writer name.
     * 
     * @return string returns to "handler" e.g. "file"
     */
    abstract public function getWriter();

    /**
     * Store log data into array
     * 
     * @param string  $level    log level
     * @param string  $message  log message
     * @param array   $context  context data
     * @param integer $priority message priority
     * 
     * @return void
     */
    abstract public function log($level, $message, $context = array(), $priority = null);

    /**
     * Emergency
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function emergency($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }

    /**
     * Alert
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function alert($message = '', $context = array(), $priority = null)
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }

    /**
     * Critical
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function critical($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }

    /**
     * Error
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function error($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }
    
    /**
     * Warning
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function warning($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }
    
    /**
     * Notice
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function notice($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }
    
    /**
     * Info
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function info($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }

    /**
     * Debug
     * 
     * @param string  $message  log message
     * @param array   $context  data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function debug($message = '', $context = array(), $priority = null) 
    {
        $this->log(__FUNCTION__, $message, $context, $priority);
    }
}