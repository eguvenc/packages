<?php

namespace Obullo\Application;

use Closure;
use Exception;
use ErrorException;
use RuntimeException;
use ReflectionFunction;
use Obullo\Error\Debug;
use Obullo\Http\Controller;
use Interop\Container\ContainerInterface as Container;

/**
 * Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Application implements ApplicationInterface
{
    const VERSION = '1.0rc1';

    protected $container;
    protected $fatalError;
    protected $exceptions = array();

    /**
     * Constructor
     * 
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set error handlers
     *
     * @return void
     */
    public function registerErrorHandlers()
    {
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * Register fatal error handler
     * 
     * @return mixed
     */
    public function registerFatalError()
    {   
        if (null != $error = error_get_last()) {  // If we have a fatal error convert to it to exception obj

            $closure = $this->getFatalCallback();
            $e = new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
            $exception = new \Obullo\Error\Exception;

            if ($this->getEnv() != 'production') {
                echo $exception->make($e);  // Print exceptions
            }
            if ($exception->isCatchable($e)) {
                $closure($e);
            }
        }
    }

    /**
     * Sets application exception errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function error(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();
        if (isset($parameters[0])) {
            $this->exceptions[] = array('closure' => $closure, 'exception' => $parameters[0]->getClass());
        }
    }

    /**
     * Sets application fatal errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function fatal(Closure $closure)
    {
        $this->fatalError = $closure;
    }

    /**
     * Error handler, convert all errors to exceptions
     * 
     * @param integer $level   name
     * @param string  $message error message
     * @param string  $file    file
     * @param integer $line    line
     * 
     * @return boolean whether to continue displaying php errors
     */
    public function handleError($level, $message, $file = '', $line = 0)
    {
        $exception = new \Obullo\Error\Exception;
        $e = new ErrorException($message, $level, 0, $file, $line);

        if ($this->getEnv() != 'production') {
            echo $exception->make($e);  // Print exceptions to see errors
        }
        return $this->exceptionError($e);
    }

    /**
     * Exception error handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function handleException(Exception $e)
    {
        $exception = new \Obullo\Error\Exception;

        if ($this->getEnv() != 'production') {
            echo $exception->make($e);  // Print exceptions to see errors
        }
        return $this->exceptionError($e);
    }

    /**
     * Exception log handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function exceptionError($e)
    {
        $exception = new \Obullo\Error\Exception;
        $return = false;
        if ($exception->isCatchable($e)) {

            foreach ($this->exceptions as $val) {
                if ($val['exception']->isInstance($e)) {
                    $return = $val['closure']($e);
                }
            }
        }
        return $return;
    }

    /**
     * Returns to fatal error closure
     * 
     * @return Closure object
     */
    public function getFatalCallback()
    {
        return $this->fatalError;
    }

    /**
     * Returns to defined exception closures in app/errors.php
     * 
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Is Cli ?
     *
     * Test to see if a request was made from the command line.
     *
     * @return bool
     */
    public function isCli()
    {
        return (PHP_SAPI === 'cli' || defined('STDIN'));
    }

    /**
     * Returns to detected environment
     * 
     * @return string
     */
    public function getEnv()
    {
        return $this->container->get('env')->getValue();
    }

    /**
     * Returns to current version of Obullo
     * 
     * @return string
     */
    public function getVersion()
    {
        return static::VERSION;
    }

    /**
     * Returns to container object
     * 
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Call controller methods from view files ( View files $this->method(); support ).
     * 
     * @param string $method    called method
     * @param array  $arguments called arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($method == '__invoke') {
            return;
        }
        if (method_exists(Controller::$instance, $method)) {
            return Controller::$instance->$method($arguments);
        }
    }

    /**
     * Returns 
     *
     * This function similar with Codeigniter getInstance(); 
     * instead of getInstance()->class->method() we use $this->c['app']->class->method();
     * 
     * @param string $key application object
     * 
     * @return object
     */
    public function __get($key)
    {
        $cid = 'app.'.$key;
        if ($this->container->has($cid) ) {
            return $this->container->get($cid);
        }
        if (class_exists('Controller', false) && Controller::$instance != null) {
            return Controller::$instance->{$key};
        }
        return $this->container->get($key);
    }

}