<?php

namespace Obullo\Application;

use Psr\Http\Message\ResponseInterface as Response;

use Obullo\Container\ContainerAwareInterface;
use Obullo\Http\Middleware\ParamsAwareInterface;
use Obullo\Http\Middleware\ControllerAwareInterface;

use ReflectionClass;

/**
 * Http Application
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Http extends Application
{
    protected $error;
    protected $controller;

    /**
     * Constructor
     *
     * @return void
     */
    public function init()
    {
        $c = $this->c;  // make global

        include APP .'errors.php';
        
        $this->registerErrorHandlers();

        include APP .'middlewares.php';
        include APP .'events.php';
        include APP .'routes.php';

        $this->boot();
    }

    /**
     * Register assigned middlewares
     * 
     * @return void
     */
    protected function boot()
    {
        $router = $this->c['router'];
        $router->init();

        include MODULES .$router->getModule('/').$router->getDirectory().'/'.$router->getClass().'.php';
        $className = '\\'.$router->getNamespace().'\\'.$router->getClass();

        $method = $router->getMethod();

        if (! class_exists($className)) {
            $router->clear();  // Fix layer errors.
            $this->error = true;
            return;
        }
        $this->controller = new $className;
        $this->controller->__setContainer($this->c);

        if (! method_exists($this->controller, $method)
            || substr($method, 0, 1) == '_'
        ) {
            $this->error = true;
            return;
        }
        $this->bootAnnotations($method);
        $this->bootMiddlewares();
    }

    /**
     * Boot middlewares
     * 
     * @return void
     */
    protected function bootMiddlewares()
    {
        $router     = $this->c['router'];
        $request    = $this->c['request'];
        $middleware = $this->c['middleware'];

        $uriString = $request->getUri()->getPath();

        foreach ($router->getAttachedMiddlewares() as $value) {

            $attachedRoute = str_replace('#', '\#', $value['attachedRoute']);  // Ignore delimiter

            if ($value['route'] == $uriString) {     // if we have natural route match
                $object = $middleware->queue($value['name']);
            } elseif (ltrim($attachedRoute, '.') == '*' || preg_match('#'. $attachedRoute .'#', $uriString)) {
                $object = $middleware->queue($value['name']);
            }
            if ($object instanceof ParamsAwareInterface && ! empty($value['options'])) {  // Inject parameters
                $object->setParams($value['options']);
            }
        }
        if ($this->c['config']['http']['debugger']['enabled']) {  // Boot debugger
            $middleware->queue('Debugger');
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
    protected function inject($middleware)
    {
        foreach ($middleware->getNames() as $name) {
            $object = $middleware->get($name);
            if ($object instanceof ContainerAwareInterface) {
                $object->setContainer($this->c);
            }
            if ($object instanceof ControllerAwareInterface) {
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
        if ($this->c['config']['controller']['annotation']) {

            $reflector = new ReflectionClass($this->controller);

            if ($reflector->hasMethod($method)) {
                $docs = new \Obullo\Application\Annotations\Controller;
                $docs->setContainer($this->c);
                $docs->setReflectionClass($reflector);
                $docs->setMethod($method);
                $docs->parse();
            }
        }
    }

    /**
     * Execute the controller
     *
     * @param Psr\Http\Message\ResponseInterface $response response
     * 
     * @return mixed
     */
    public function call(Response $response)
    {
        if ($this->error) {
            return false;
        }
        unset($this->c['response']);
        $this->c['response'] = function () use ($response) {
            return $response;
        };
        $result = call_user_func_array(
            array(
                $this->controller,
                $this->c['router']->getMethod()
            ),
            array_slice($this->controller->request->getUri()->getRoutedSegments(), 3)
        );
        if ($result instanceof Response) {
            $response = $result;
        }
        return $response;   
    }

}