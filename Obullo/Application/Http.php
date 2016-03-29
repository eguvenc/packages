<?php

namespace Obullo\Application;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Obullo\Container\ParamsAwareInterface;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Http\Controller\ControllerAwareInterface;

use Exception;
use ErrorException;
use ReflectionClass;
use Obullo\Tests\HttpTestInterface;
use Obullo\Router\RouterInterface as Router;
use Obullo\Application\MiddlewareStackInterface as Middleware;

/**
 * Http Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Http extends Application
{
    /**
     * Dispatch error
     * 
     * @var boolean
     */
    protected $error;

    /**
     * Current controller
     * 
     * @var object
     */
    protected $controller;

    /**
     * Constructor
     *
     * @return void
     */
    public function init()
    {
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(array($this, 'handleFatalError'));

        $container = $this->getContainer();  // Make global
        $app = $container->get('app');
        
        include APP .'providers.php';

        $container->share('router', 'Obullo\Router\Router')
            ->withArgument($container)
            ->withArgument($container->get('request'))
            ->withArgument($container->get('logger'));

        $middleware = $container->get('middleware');

        include APP .'middlewares.php';

        $router = $container->get('router');

        include APP .'routes.php';

        $this->boot($router, $middleware);
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
        $this->showException($e);
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
        $this->showException($e);
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
            $this->showException($e);
        }
    }

    /**
     * Show exception
     * 
     * @param Exception $e object
     * 
     * @return void
     */
    protected function showException(Exception $e)
    {
        $container = $this->getContainer();

        $error = $container->get('middleware')->add('Error');
        $error->setContainer($container);

        $error($e, $container->get('request'), $container->get('response'));
    }

    /**
     * Register assigned middlewares
     *
     * @param object $router     router
     * @param object $middleware middleware
     * 
     * @return void
     */
    protected function boot(Router $router, Middleware $middleware)
    {
        $router->init();
        $file = FOLDERS .$router->getAncestor('/').$router->getFolder('/').$router->getClass().'.php';

        $className = '\\'.$router->getNamespace().$router->getClass();
        $method    = $router->getMethod();

        if (! is_file($file)) {
            $router->clear();  // Fix layer errors.
            $this->error = true;
        } else {
            include $file;
            $this->controller = new $className($this->container);
            $this->controller->container = $this->container;

            if (! method_exists($this->controller, $method)
                || substr($method, 0, 1) == '_'
            ) {
                $router->clear();  // Fix layer errors.
                $this->error = true;
            }
        }
        $this->bootAnnotations($method);
        $this->bootMiddlewares($router, $middleware);
    }

    /**
     * Boot middlewares
     * 
     * @param object $router     router
     * @param object $middleware middleware
     * 
     * @return void
     */
    protected function bootMiddlewares(Router $router, Middleware $middleware)
    {
        $object = null;
        $request = $this->container->get('request');
        $uriString = $request->getUri()->getPath();

        if ($attach = $router->getAttach()) {

            foreach ($attach->getArray() as $value) {

                $attachRegex = str_replace('#', '\#', $value['attach']);  // Ignore delimiter

                if ($value['route'] == $uriString) {     // if we have natural route match
                    $object = $middleware->add($value['name']);
                } elseif (ltrim($attachRegex, '.') == '*' || preg_match('#'. $attachRegex .'#', $uriString)) {
                    $object = $middleware->add($value['name']);
                }
                if ($object instanceof ParamsAwareInterface && ! empty($value['options'])) {  // Inject parameters
                    $object->setParams($value['options']);
                }
            }
        }
        if ($this->container->get('config')['extra']['debugger']) {  // Boot debugger
            $middleware->add('Debugger');
        }
        $this->inject($middleware);
    }

    /**
     * Inject controller object
     * 
     * @param object $middleware middleware
     * 
     * @return void
     */
    protected function inject(Middleware $middleware)
    {
        foreach ($middleware->getNames() as $name) {
            $object = $middleware->get($name);
            if ($object instanceof ContainerAwareInterface) {
                $object->setContainer($this->getContainer());
            }
            if ($this->controller != null && $object instanceof ControllerAwareInterface) {
                $object->setController($this->controller);
            }
        }
    }

    /**
     * Read controller annotations
     * 
     * @param string $method method
     * 
     * @return void
     */
    protected function bootAnnotations($method)
    {
        if ($this->container->get('config')['extra']['annotations'] && $this->controller != null) {
            $reflector = new ReflectionClass($this->controller);
            if ($reflector->hasMethod($method)) {
                $docs = new \Obullo\Application\Annotations\Controller;
                $docs->setContainer($this->getContainer());
                $docs->setReflectionClass($reflector);
                $docs->setMethod($method);
                $docs->parse();
            }
        }
    }

    /**
     * Execute the controller
     *
     * @param Psr\Http\Message\RequestInterface  $request  request
     * @param Psr\Http\Message\ResponseInterface $response response
     * 
     * @return mixed
     */
    public function call(Request $request, Response $response)
    {
        if ($this->error) {
            return false;
        }
        $this->container->share('response', $response);  // Refresh objects
        $this->container->share('request', $request);

        $router = $this->container->get('router');

        $result = call_user_func_array(
            array(
                $this->controller,
                $router->getMethod()
            ),
            array_slice($this->controller->request->getUri()->getRoutedSegments(), $router->getArgumentFactor())
        );

        if ($router->getMethod() != 'index' && $this->controller instanceof HttpTestInterface) {
            $result = $this->controller->__generateTestResults();
        }
        if ($result instanceof Response) {
            return $result;
        }
        return $response;   
    }

}