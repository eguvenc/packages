<?php

namespace Obullo\Application;

use Exception;
use ErrorException;
use Obullo\Http\Zend\Stratigility\MiddlewarePipe;

/**
 * Run Cli Application ( Warning : Http middlewares & Layers disabled in Cli mode.)
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Cli extends MiddlewarePipe
{
    /**
     * Constructor
     *
     * @param object $container container
     */
    public function __construct($container)
    {
        $this->initEnvironment($container);
        $this->initServices($container);

        parent::__construct($container);

        $this->initApplication($container);
    }

    /**
     * Init main services
     * 
     * @param container $container container
     * 
     * @return void
     */
    protected function initServices($container)
    {
        $container->share('config', 'Obullo\Config\Config')->withArgument($container);
        $container->share('middleware', 'Obullo\Cli\NullMiddleware');
        $container->share('request', 'Obullo\Cli\NullRequest');
        $container->share('response', 'Obullo\Cli\NullResponse');
    }

    /**
     * Initialize to Application
     *
     * @param object $container container
     * 
     * @return void
     */
    public function initApplication($container)
    {
        include APP .'providers.php';

        $logger  = $container->get('logger');
        $request = $container->get('request');

        $container->share('router', 'Obullo\Cli\Router')
            ->withArgument($request->getUri())
            ->withArgument($logger);

        if ($container->has('translator')) {
            $translator = $container->get('translator');
            $translator->setLocale($translator->getDefault());  // Set default translation
        }
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(
            function () use ($logger) {
                $this->handleFatalError();
            }
        );
        $this->call();
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
        $e = new ErrorException($message, $level, 0, $file, $line);
        $this->printError($e);
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
        $this->printError($e);
    }

    /**
     * Handle fatal errors
     * 
     * @return mixed
     */
    public function handleFatalError()
    {   
        if (null != $error = error_get_last()) {
            $e = new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
            $this->printError($e);
        }
    }

    /**
     * Print exceptions
     * 
     * @param object $e exception
     * 
     * @return void
     */
    protected function printError($e)
    {
        $log = new \Obullo\Utils\Error\Log($this->container->get('logger'));
        $log->message($e);

        echo "\33[1;31mException Error\n". $e->getMessage()."\33[0m\n";
        echo "\33[0;31m".$e->getCode().' '.$e->getFile(). ' Line : ' . $e->getLine() ."\33[0m\n";
        echo "\33[0m";
    }
    
    /**
     * Run
     *
     * This method invokes the middleware stack, including the core application;
     * the result is an array of HTTP status, header, and output.
     * 
     * @return void
     */
    public function call()
    {    
        $router  = $this->container->get('router');
        $logger  = $this->container->get('logger');
        $request = $this->container->get('request');

        $router->init();
        $className = $router->getNamespace();
        
        $controller = new $className($this->container);  // Call the controller
        $controller->container = $this->container;

        if (! method_exists($className, $router->getMethod())) {
            echo Console::fail('The method "' .$router->getMethod(). '" not found.');
            die;
        }
        $arguments = array_slice(
            $request->getUri()->getSegments(),
            2
        );
        call_user_func_array(
            array(
                $controller,
                $router->getMethod()
            ), 
            $arguments
        );
        if (isset($_SERVER['argv'])) {
            $logger->debug('php '.implode(' ', $_SERVER['argv']));
        }
    }

}