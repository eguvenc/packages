<?php

namespace Obullo\Application;

/**
 * Run Cli Application ( Warning : Http middlewares & Layers disabled in Cli mode.)
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Cli extends Application
{
    /**
     * Constructor
     *
     * @return void
     */
    public function init()
    {
        $c = $this->c;
        include APP .'errors.php';

        $this->registerErrorHandlers();

        $logger     = $c['logger'];
        $request    = $c['request'];
        $translator = $c['translator'];

        unset($c['router']);   // Replace router component

        $c['router'] = function () use ($request, $logger) {
            return new \Obullo\Cli\Router($request->getUri(), $logger);
        };
        include APP .'events.php';

        $translator->setLocale($translator->getDefault());  // Set default translation

        register_shutdown_function(
            function () use ($logger) {
                $this->registerFatalError();
                $logger->shutdown();
            }
        );
    }

    /**
     * Run
     *
     * This method invokes the middleware stack, including the core application;
     * the result is an array of HTTP status, header, and output.
     * 
     * @return void
     */
    public function run()
    {    
        $this->init();

        $router  = $this->c['router'];
        $logger  = $this->c['logger'];
        $request = $this->c['request'];

        $router->init();
        $className = $router->getNamespace();

        if (! class_exists($className, false)) {
            $this->router->classNotFound();
        }
        $controller = new $className;  // Call the controller
        $controller->__setContainer($this->c);
        
        if (! method_exists($className, $router->getMethod())) {
            $this->router->methodNotFound();
        }
        $arguments = array_slice($request->getUri()->getSegments(), 2);

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