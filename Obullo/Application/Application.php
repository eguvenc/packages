<?php

namespace Obullo\Application;

use Closure;
use Exception;
use ErrorException;
use RuntimeException;
use ReflectionFunction;
use Obullo\Error\Debug;
use Obullo\Http\Controller;
use Obullo\Container\Dependency;
use Obullo\Container\ContainerInterface as Container;

/**
 * Application
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Application implements ApplicationInterface
{
    const VERSION = '1.0rc1';

    protected $c;
    protected $fatalError;
    protected $exceptions = array();
    protected $dependency;

    /**
     * Constructor
     * 
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
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
            
            if ($this->c['app.env'] != 'production') {
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

        if ($this->c['app.env'] != 'production') {
            echo $exception->make($e);  // Print exceptions to see errors
        }
        return $this->logException($e);
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

        if ($this->c['app.env'] != 'production') {
            echo $exception->make($e);  // Print exceptions to see errors
        }
        return $this->logException($e);
    }

    /**
     * Exception log handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function logException($e)
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
    public function env()
    {
        return $this->c['app.env'];
    }

    /**
     * Registers a service provider.
     *
     * @param array $providers provider name and namespace array
     *
     * @return object
     */
    public function provider(array $providers)
    {
        $this->c->provider($providers);
        return $this;
    }

    /**
     * Register services
     * 
     * @param array $services services
     * 
     * @return object
     */
    public function service(array $services)
    {
        $this->c->service($services);
        return $this;
    }

    /**
     * Register components & resolve dependencies
     *
     * @param array $namespaces component class name & namespaces
     * 
     * @return void
     */
    public function component(array $namespaces)
    {   
        $this->dependency = $this->c['dependency'];
        foreach ($namespaces as $cid => $Class) {
            $this->dependency->addComponent($cid, $Class);
            $this->dependency->addDependency($cid);
        }
        return $this;
    }

    /**
     * Creates dependency
     * 
     * @param array $deps dependencies
     * 
     * @return object
     */
    public function dependency(array $deps)
    {
        foreach ($deps as $cid) {
            $this->dependency->addDependency($cid);
        }
        return $this;
    }

    /**
     * Removes dependency
     * 
     * @param array $deps dependencies
     * 
     * @return object
     */
    public function removeDependency(array $deps)
    {
        foreach ($deps as $cid) {
            $this->dependency->removeDependency($cid);
        }
        return $this;
    }


    /**
     * Returns current version of Obullo
     * 
     * @return string
     */
    public function version()
    {
        return static::VERSION;
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
        return Controller::$instance->$method($arguments);
    }

    /**
     * Returns 
     *
     * This function similar with Codeigniter getInstance(); 
     * instead of getInstance()->class->method() we use $this->c['app']->class->metod();
     * 
     * @param string $key application object
     * 
     * @return object
     */
    public function __get($key)
    {
        $cid = 'app.'.$key;
        if ($this->c->has($cid) ) {
            return $this->c[$cid];
        }
        if (class_exists('Controller', false) && Controller::$instance != null) {
            return Controller::$instance->{$key};
        }
        return $this->c[$key];
    }

}