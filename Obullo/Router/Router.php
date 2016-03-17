<?php

namespace Obullo\Router;

use Closure;
use LogicException;
use Obullo\Http\Controller;
use Obullo\Log\LoggerInterface as Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Interop\Container\ContainerInterface as Container;

use Obullo\Router\Route\Route;
use Obullo\Router\Route\Group;
use Obullo\Router\Route\Attach;
use Obullo\Router\Route\Parameters;
use Obullo\Router\Resolver\FolderResolver;
use Obullo\Router\Resolver\AncestorResolver;
use Obullo\Router\Resolver\ClassResolver;

/**
 * Http Router Class ( Modeled after Codeigniter router )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router implements RouterInterface
{
    protected $uri;                          // Uri class
    protected $domain;                       // Domain object
    protected $logger;                       // Logger class
    protected $class = '';                   // Controller class name
    protected $route;                        // Route object
    protected $attach;                       // Attachment object
    protected $container;                    // Container
    protected $folder = '';                  // Folder name
    protected $ancestor = '';                // Ancestor foldername
    protected $method = 'index';             // Default method is index and its immutable !
    protected $defaultController = '';       // Default controller name
    protected $argumentFactor;               // Argument slice factor
    protected $group;                        // Group object
    PROTECTED $domainName;

    /**
     * Constructor
     * 
     * Runs the route mapping function.
     * 
     * @param array  $container \Obullo\Container\ContainerInterface
     * @param object $request   \Psr\Http\Message\RequestInterface
     * @param array  $logger    \Obullo\Log\LoggerInterface
     */
    public function __construct(Container $container, Request $request, Logger $logger)
    {
        $this->container = $container;
        $this->uri = $request->getUri();
        $this->logger = $logger;
        $this->domain = new Domain;
        $this->domain->setHost($this->uri->getHost());

        $this->logger->debug('Request Uri', ['uri' => $this->uri->getPath()], 9999);
        $this->logger->debug('Router Class Initialized', array('host' => $this->uri->getHost()), 9998);
    }

    /**
     * Clean all data for Layers
     *
     * @return void
     */
    public function clear()
    {
        $this->uri = $this->container->get('request')->getUri();   // Reset cloned URI object.
        $this->class = '';
        $this->folder = '';
        $this->ancestor = '';
    }

    /**
     * Sets default page controller
     * 
     * @param string $page uri
     * 
     * @return return object router
     */
    public function defaultPage($page)
    {
        $this->defaultController = $page;
        return $this;
    }

    /**
     * Set immutable root domain
     * 
     * @param string $domain name
     * 
     * @return return object router
     */
    public function domainRoot($domain)
    {
        $this->domain->setImmutable($domain);
        return $this;
    }

    /**
     * Set the route mapping
     *
     * @return void
     */
    public function init()
    {
        // Is there a URI string ? 
        // If not, the default controller specified in the "routes" file will be shown.

        if ($this->uri->getPath() == '/') {
            if (empty($this->defaultController)) {
                return;
            }
            $resolver = $this->resolve(explode('/', $this->defaultController));  // Turn the default route into an array.
            $segments = $resolver->getSegments();
            $class    = empty($segments[1]) ? $segments[0] : $segments[1];

            $this->setClass($class);
            $this->setMethod('index');
            $this->uri->setRoutedSegments($segments);  // Assign the segments to the URI class
            $this->logger->debug('No URI present. Default controller set.');
            return;
        }
        $this->dispatch();
    }

    /**
     * Creates http routes
     * 
     * @param string $methods method names
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    protected function route($methods, $match, $rewrite = null, $closure = null)
    {
        if (is_object($this->group)) {
            $options = $this->group->getOptions();
            if ($this->domain->match($options) === false && $options['domain'] !== null) {
                return;
            }
        }
        if ($this->route == null) {
            $this->route = new Route($this);
        }
        $this->route->add(
            $methods,
            $match,
            $rewrite,
            $closure
        );
        return $this;
    }

    /**
     * Dispatch routes if we have no errors
     * 
     * @return void
     */
    protected function dispatch()
    {
        $this->uri->parseSegments();   // Compile the segments into an array 

        if (! is_null($this->route) && $this->route->isEmpty()) {
            $this->setRequest($this->uri->getSegments());
            return;
        }
        $this->parseRoutes();            // Parse any custom routing that may exist
    }

    /**
     * Detect class && method names
     *
     * This function takes an array of URI segments as
     * input, and sets the current class/method
     *
     * @param array $segments segments
     * 
     * @return void
     */
    public function setRequest($segments = array())
    {
        $resolver = $this->resolve($segments);
        if ($resolver == null) {
            return;
        }
        $factor   = $resolver->getFactor();
        $segments = $resolver->getSegments();

        $one = 1 + $factor;
        $two = 2 + $factor;

        if (! empty($segments[$one])) {
            $this->setClass($segments[$one]);
        }
        if (! empty($segments[$two])) {
            $this->setMethod($segments[$two]); // A standard method request
        } else {
            $segments[$two] = 'index';         // This lets the "routed" segment array identify that the default index method is being used.
            $this->setMethod('index');
        }
        $this->argumentFactor = (3 + $factor);
        $this->uri->setRoutedSegments($segments);  // Update our "routed" segment array to contain the segments.
    }

    /**
     * Resolve segments
     * 
     * @param array $segments uri parts
     * 
     * @return array|null
     */
    protected function resolve($segments)
    {
        if (empty($segments[0])) {
            return null;
        }
        $this->setFolder($segments[0]);      // Set first segment as default folder
        $segments = $this->checkAncestor($segments);
        $ancestor = $this->getAncestor('/');

        if (empty($ancestor)) {

            if (is_dir(FOLDERS .$this->getFolder().'/')) {
                $resolver = new FolderResolver($this);
                return $resolver->resolve($segments);
            }
            $this->setFolder(null);
            $resolver = new ClassResolver($this);
            return $resolver->resolve($segments);
        }
        $resolver = new AncestorResolver($this);
        return $resolver->resolve($segments);
    }

    /**
     * Returns to argument slice factor
     * 
     * @return integer
     */
    public function getArgumentFactor()
    {
        return $this->argumentFactor;
    }

    /**
     * Check first segment if have a ancestor folder & set it.
     * 
     * @param array $segments uri segments
     * 
     * @return array
     */
    protected function checkAncestor($segments)
    {
        if (! empty($segments[1])
            && strtolower($segments[1]) != 'views'  // http://example/debugger/view/index bug fix
            && is_dir(FOLDERS .$segments[0].'/'. $segments[1].'/')  // Detect ancestor folder and change folder !!
        ) {
            $this->setAncestor($segments[0]);
            $this->setFolder($segments[1]);
            array_shift($segments);
        }
        return $segments;
    }

    /**
     * Parse Routes
     *
     * This function matches any routes that may exist in the routes.php file against the URI to
     * determine if the folder/class need to be remapped.
     *
     * @return void
     */
    protected function parseRoutes()
    {
        $uri = ltrim($this->uri->getPath(), '/'); // fix route errors with trim()

        if (is_object($this->route) && $routes = $this->route->getArray()) {
            foreach ($routes as $val) {   // Loop through the route array looking for wild-cards
                
                $parameters = Parameters::parse($uri, $val);

                if ($this->hasRegexMatch($val['match'], $uri)) {    // Does the route match ?
                    $this->dispatchRouteMatches($uri, $val, $parameters);
                    return;
                }
            }
        }
        $this->setRequest($this->uri->getSegments());  // If we got this far it means we didn't encounter a matching route so we'll set the site default route
    }

    /**
     * Dispatch route matches and assign middlewares
     * 
     * @param string $uri        current uri
     * @param array  $val        route values
     * @param array  $parameters closure parameters
     * 
     * @return void
     */
    protected function dispatchRouteMatches($uri, $val, $parameters)
    {
        if (count($val['when']) > 0) {  //  Dynamically add method not allowed middleware
            $this->container->get('middleware')->add('NotAllowed')->setParams($val['when']);
        }
        // Do we have a back-reference ?
        if (! empty($val['rewrite']) && strpos($val['rewrite'], '$') !== false 
            && strpos($val['match'], '(') !== false
        ) {
            $val['rewrite'] = preg_replace('#^'.$val['match'].'$#', $val['rewrite'], $uri);
        }
        $segments = (empty($val['rewrite'])) ? $this->uri->getSegments() : explode('/', $val['rewrite']);

        $this->setRequest($segments);
        $this->bind($val['closure'], $parameters, true);
    }

    /**
     * Replace route scheme
     * 
     * @param array $replace scheme data
     * 
     * @return object
     */
    public function where(array $replace)
    {   
        $this->route->addWhere($replace);
        return $this;
    }

    /**
     * Closure bind function
     * 
     * @param object  $closure         anonymous function
     * @param array   $args            arguments
     * @param boolean $useCallUserFunc whether to use call_user_func_array()
     * 
     * @return void
     */
    protected function bind($closure, $args = array(), $useCallUserFunc = false)
    {
        if (! is_callable($closure)) {
            return;
        }
        if (Controller::$instance != null) {
            $closure = Closure::bind($closure, Controller::$instance, 'Obullo\Http\Controller');
        }
        if ($useCallUserFunc) {
            return call_user_func_array($closure, $args);
        }
        return $closure($args);
    }

    /**
     * Set the class name
     * 
     * @param string $class classname segment 1
     *
     * @return object Router
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set current method
     * 
     * @param string $method name
     *
     * @return object Router
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Set the folder name : It must be lowercase otherwise folder does not work
     *
     * @param string $folder folder
     * 
     * @return object Router
     */
    public function setFolder($folder)
    {
        $this->folder = strtolower($folder);
        return $this;
    }

    /**
     * Sets top folder http://example.com/api/user/delete/4
     * 
     * @param string $folder sets top folder
     *
     * @return void
     */
    public function setAncestor($folder)
    {
        $this->ancestor = strtolower($folder);
    }

    /**
     * Get primary folder
     *
     * @param string $separator get folder seperator
     * 
     * @return void
     */
    public function getAncestor($separator = '')
    {
        return (empty($this->ancestor)) ? '' : htmlspecialchars($this->ancestor).$separator;
    }

    /**
     * Get folder
     *
     * @param string $separator get folder seperator
     * 
     * @return string
     */
    public function getFolder($separator = '')
    {
        return (empty($this->folder)) ? '' : htmlspecialchars($this->folder).$separator;
    }

    /**
     * Returns to current routed class name
     *
     * @return string
     */
    public function getClass()
    {
        return htmlspecialchars($this->ucwordsUnderscore($this->class));
    }

    /**
     * Returns to current method
     * 
     * @return string
     */
    public function getMethod()
    {
        return htmlspecialchars($this->method);
    }

    /**
     * Returns php namespace of the current route
     * 
     * @return string
     */
    public function getNamespace()
    {
        $folder = $this->getFolder();
        if (strpos($folder, "/") > 0) {  // Converts "Tests\Authentication/storage" to Tests\Authentication\Storage
            $exp = explode("/", $folder);
            $folder = $exp[0]."\\".ucfirst(end($exp));
        }
        $namespace = $this->ucwordsUnderscore($this->getAncestor()).'\\'.$this->ucwordsUnderscore($folder);
        $namespace = trim($namespace, '\\');
        return (empty($namespace)) ? '' : $namespace.'\\';
    }

    /**
     * Check regex regex match
     * 
     * @param string $match regex or string
     * @param string $uri   current uri
     * 
     * @return boolean
     */
    public function hasRegexMatch($match, $uri)
    {
        if ($match == $uri) { // Is there any literal match ? 
            return true;
        }
        if (preg_match('#^'.$match.'$#', $uri)) {
            return true;
        }
        return false;
    }

    /**
     * Replace underscore to spaces to use ucwords
     * 
     * Before  : widgets\tutorials_a 
     * Ucwords : widgets\Tutorials A
     * Final   : Widgets\Tutorials_A
     * 
     * @param string $string    namespace part
     * @param string $delimiter default underscore "_"
     * 
     * @return void
     */
    public function ucwordsUnderscore($string, $delimiter = "_")
    {
        $str = str_replace($delimiter, ' ', $string);
        $str = ucwords($str);
        return str_replace(' ', $delimiter, $str);
    }

    /**
     * Set domain name for route group
     * 
     * @param string $domain name
     * 
     * @return object
     */
    public function domain($domain)
    {
        $this->domainName = $domain;
        return $this;
    }

    /**
     * Set grouped routes, options like middleware
     * 
     * @param string $uri     match route
     * @param object $closure which contains $this->attach(); methods
     * @param array  $options domain, directions and middleware name
     * 
     * @return object of group
     */
    public function group($uri, $closure = null, $options = array())
    {
        if (is_callable($uri)) {
            $options = $closure;
            $closure = $uri;
            $uri = null;
        }
        if ($this->group == null) {
            $this->group = new Group($this, $this->uri);
        }
        if ($this->domainName != null) {
            $options['domain'] = $this->domainName;
        }
        $this->group->add($uri, $closure, $options);
        $this->domainName = null;  // Reset domain name

        return $this->group;
    }

    /**
     * Attach route to middleware
     * 
     * @param string $route middleware route
     * 
     * @return object
     */
    public function attach($route)
    {
        if ($this->attach == null) {
            $this->attach = new Attach($this);
        }
        $this->attach->add($route);
        return $this;
    }

    /**
     * Assign middleware(s) to current route
     * 
     * @param mixed $middlewares array|string
     * 
     * @return object
     */
    public function middleware($middlewares)
    {
        if ($this->attach == null) {
            $this->attach = new Attach($this);
        }
        $this->attach->toRoute($middlewares);
        return $this;
    }

    /**
     * Returns to attachment object
     * 
     * @return object
     */
    public function getAttach()
    {
        return $this->attach;
    }

    /**
     * Returns to route object
     * 
     * @return array
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Returns to group object
     * 
     * @return object
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Returns to domain object
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns to default contoller configured in router middleware
     * 
     * @return string
     */
    public function getDefaultPage()
    {
        return $this->defaultController;
    }

    /**
     * Creates http GET based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function get($match, $rewrite = null, $closure = null)
    {
        $this->route(array('get'), $match, $rewrite, $closure);
        return $this;
    }

    /**
     * Creates http POST based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function post($match, $rewrite = null, $closure = null)
    {
        $this->route(array('post'), $match, $rewrite, $closure);
        return $this;
    }

    /**
     * Creates http PUT based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function put($match, $rewrite = null, $closure = null)
    {
        $this->route(array('put'), $match, $rewrite, $closure);
        return $this;
    }

    /**
     * Creates http DELETE based route
     * 
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function delete($match, $rewrite = null, $closure = null)
    {
        $this->route(array('delete'), $match, $rewrite, $closure);
        return $this;
    }

    /**
     * Creates multiple http route
     * 
     * @param string $methods http methods
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function match($methods, $match, $rewrite = null, $closure = null)
    {
        $this->route($methods, $match, $rewrite, $closure);
        return $this;
    }

}