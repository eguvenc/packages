<?php

namespace Obullo\Application;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use ReflectionClass;
use Obullo\Tests\HttpTestInterface;
use Obullo\Container\ParamsAwareInterface;
use Obullo\Router\RouterInterface as Router;
use Obullo\Http\Zend\Stratigility\MiddlewarePipe;

/**
 * Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class App extends MiddlewarePipe
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
        $this->initServerRequest($container);
        $this->initTestServer($container);
        $this->initApplication($container);

        parent::__construct($container);
    }

    /**
     * Create main services
     * 
     * @param object $container container
     * 
     * @return void
     */
    protected function initServices($container)
    {
        $container->share('config', 'Obullo\Config\Config')->withArgument($container);
        $container->share('middleware', 'Obullo\Application\Middleware')->withArgument($container);
    }

    /**
     * Create server request
     * 
     * @param object $container container
     * 
     * @return void
     */
    protected function initServerRequest($container)
    {
        $request = \Obullo\Http\ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $request->setContainer($container);

        $container->share('request', $request);
        $container->share('response', 'Obullo\Http\Response');
    }

    /**
     * Detect test server
     * 
     * @param object $container container
     * 
     * @return void
     */
    protected function initTestServer($container)
    {
        if (defined('STDIN') && ! empty($_SERVER['argv'][0]) && $_SERVER['SCRIPT_FILENAME'] == 'public/index.php') {
            $testEnvironment = new \Obullo\Tests\TestEnvironment;
            $testEnvironment->createServer();
            $testEnvironment->setContainer($container);
        }
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

        $middleware = $container->get('middleware'); // Make global

        include APP .'middlewares.php';
    }

    /**
     * Add middlewares
     * 
     * @param [type] $middleware [description]
     * @param array  $params     [description]
     */
    public function add($middleware, $params = array())
    {
        // spl object storage has ???

        $this->pipeline->enqueue(
            [
                'callable' => $middleware,
                'params' => $params
            ]
        );
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