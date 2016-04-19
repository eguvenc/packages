<?php

namespace Obullo\Application;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use ReflectionClass;
use Obullo\Tests\HttpTestInterface;
use Obullo\Container\ParamsAwareInterface;
use Obullo\Router\RouterInterface as Router;

/**
 * Http Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Http extends Application
{
    /**
     * Constructor
     *
     * @return void
     */
    public function init()
    {
        $container = $this->getContainer(); // Make global

        $app = $container->get('app');  // Make global
        
        include APP .'providers.php';

        $container->share('router', 'Obullo\Router\Router')
            ->withArgument($container)
            ->withArgument($container->get('request'))
            ->withArgument($container->get('logger'));

        $middleware = $container->get('middleware'); // Make global

        include APP .'middlewares.php';

        $router = $container->get('router'); // Make global

        include APP .'routes.php';

        $container->get('router')->init();

        $this->bootMiddlewares();
    }

    /**
     * Boot middlewares
     * 
     * @return void
     */
    protected function bootMiddlewares()
    {
        $object = null;
        $request    = $this->container->get('request');
        $router     = $this->container->get('router');
        $middleware = $this->container->get('middleware');

        $uriString = $request->getUri()->getPath();

        if ($attach = $router->getAttach()) {
            
            foreach ($attach->getArray() as $value) {
                
                $attachRegex = str_replace('#', '\#', $value['attach']);  // Ignore delimiter

                if ($value['route'] == $uriString) {     // if we have natural route match
                    $object = $middleware->add($value['name'], $value['params']);
                } elseif (ltrim($attachRegex, '.') == '*' || preg_match('#'. $attachRegex .'#', $uriString)) {
                    $object = $middleware->add($value['name'], $value['params']);
                }
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
        $router = $this->container->get('router');

        $file      = FOLDERS .$router->getAncestor('/').$router->getFolder('/').$router->getClass().'.php';
        $className = '\\'.$router->getNamespace().$router->getClass();
        $method    = $router->getMethod();

        if (! is_file($file)) {
            $router->clear();  // Fix layer errors.
            return false;

        } else {

            include $file;

            $controller = new $className($this->container);
            $controller->container = $this->container;

            if (method_exists($controller, '__invoke')) {  // Assign layout variables
                $controller();
            }
            if (! method_exists($controller, $method)
                || substr($method, 0, 1) == '_'
            ) {
                $router->clear();  // Fix layer errors.
                return false;
            }
        }
        $this->container->share('response', $response);  // Refresh objects
        $this->container->share('request', $request);

        $router = $this->container->get('router');

        $result = call_user_func_array(
            array(
                $controller,
                $router->getMethod()
            ),
            array_slice($controller->request->getUri()->getRoutedSegments(), $router->getArity())
        );
        if ($router->getMethod() != 'index' && $controller instanceof HttpTestInterface) {
            $result = $controller->__generateTestResults();
        }
        if ($result instanceof Response) {
            return $result;
        }

        return $response;   
    }

}