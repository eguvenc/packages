<?php

namespace Obullo\Application;

/**
 * Run Cli Application ( Warning : Http middlewares & Layers disabled in Cli mode.)
 * 
 * @copyright 2009-2016 Obullo
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
        $container = $this->getContainer();
        $app = $container->get('app');

        include APP .'errors.php';

        $this->registerErrorHandlers();

        $logger     = $container->get('logger');
        $request    = $container->get('request');

        $container->share('router', 'Obullo\Cli\Router')
            ->withArgument($request->getUri())
            ->withArgument($logger);

        if ($container->has('translator')) {
            $translator = $container->get('translator');
            $translator->setLocale($translator->getDefault());  // Set default translation
        }
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

        $router  = $this->container->get('router');
        $logger  = $this->container->get('logger');
        $request = $this->container->get('request');

        $router->init();
        $className = $router->getNamespace();

        if (! class_exists($className, false)) {
            $this->router->classNotFound();
        }
        $controller = new $className;  // Call the controller
        $controller->setContainer($this->container);
        
        if (! method_exists($className, $router->getMethod())) {
            $this->router->methodNotFound();
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