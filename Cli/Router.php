<?php

namespace Obullo\Cli;

use Obullo\Cli\UriInterface;
use Obullo\Log\LoggerInterface;

/**
 * Cli Router Class ( ! Warning : Midllewares & Layers Disabled in CLI mode )
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router
{
    protected $uri;
    protected $logger;                         // Logger class
    protected $class = '';                     // Controller class name
    protected $routes = array();               // Routes config
    protected $method = 'index';               // Default method
    protected $directory = '';                 // Directory name
    protected $module = '';                    // Module name
    protected $classNamespace;
    protected $defaultController = 'Help';     // Default controller name
    protected $HOST;                           // Host address user.example.com

    /**
     * Constructor
     * 
     * Runs the route mapping function.
     * 
     * @param object $uri    \Obullo\Cli\UriInterface
     * @param array  $logger \Obullo\Log\LoggerInterface
     */
    public function __construct(UriInterface $uri, LoggerInterface $logger)
    {
        $this->uri = $uri;
        $this->logger = $logger;

        $this->parseCli();
        $this->logger->debug('Cli Router Class Initialized', array('host' => $this->HOST), 9998);
    }

    /**
     * Parse command line interface uri
     * 
     * @return void
     */
    protected function parseCli()
    {
        $this->uri->init();
        $this->setCliHeaders($this->uri->getPath(false));
    }

    /**
     * Returns to console uri string
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->uriString;
    }

    /**
     * Set fake headers for cli
     *
     * @param string $uriString valid uri
     * 
     * @return void
     */
    protected function setCliHeaders($uriString)
    {        
        $this->uriString = $uriString;
        if ($host = $this->uri->argument('host')) {
            $this->HOST = $host;
        }
        $_SERVER['HTTP_USER_AGENT'] = 'Cli';       /// Define cli headers for any possible isset errors.
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_HOST'] = $this->HOST;
        $_SERVER['ORIG_PATH_INFO'] = $_SERVER['QUERY_STRING'] = $_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'] = $uriString;
    }

    /**
     * Clean all data for Layers
     *
     * @return void
     */
    public function clear()
    {
        $this->class = '';
        $this->directory = '';
        $this->module = '';
    }

    /**
     * Set the route mapping ( Access must be public for Layer Class. )
     *
     * This function determines what should be served based on the URI request,
     * as well as any "routes" that have been set in the routing config file.
     *
     * @return void
     */
    public function init()
    {   
        if ($this->getPath() == '') {     // Is there a URI string ? // If not, the default controller specified in the "routes" file will be shown.
            $segments = $this->validateRequest(explode('/', $this->defaultController));  // Turn the default route into an array.
            $this->setClass($segments[0]);
            $this->setMethod('index');
            $this->logger->debug('No URI present. Default controller set.');
            return;
        }
        $this->setRequest($this->explodeSegments());  // If we got this far it means we didn't encounter a matching route so we'll set the site default route
    }

    /**
     * Explode the URI Segments. The individual segments will
     * be stored in the $this->segments array.
     *
     * @return void
     */
    public function explodeSegments()
    {
        $segments = array();
        foreach (explode('/', $this->getPath()) as $val) {
            $val = trim($val);
            if ($val != '') {
                $segments[] = $val;
            }
        }
        return $segments;
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
        $segments = $this->resolve($segments);
        if (count($segments) == 0) {
            return;
        }
        $this->setClass($segments[0]);
        if (! empty($segments[1])) {
            $this->setMethod($segments[1]); // A standard method request
        } else {
            $this->setMethod('index');
        }
    }

    /**
     * Validates the supplied segments. Attempts to determine the path to
     * the controller.
     *
     * Segments:  0 = directory, 1 = controller, 2 = method
     *
     * @param array $segments uri segments
     * 
     * @return array
     */
    public function resolve($segments)
    {
        if (! isset($segments[0])) {
            return $segments;
        }
        $Class = self::ucwordsUnderscore($segments[0]);

        if (! empty($segments[0]) && file_exists(TASKS .$Class.'.php')) {
            $this->classNamespace = '\\Tasks\\'.$Class;
            include_once TASKS .$Class.'.php';
            return $segments;
        }
        if (! empty($segments[0]) && file_exists(OBULLO .'Cli/Task/'.$Class.'.php')) {
            $this->classNamespace = '\\Obullo\Cli\Task\\'.$Class;
            include_once OBULLO.'Cli/Task/'.$Class.'.php';
            return $segments;
        }
        return $this->classNotFound();
    }

    /**
     * Returns php namespace of the current route
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->classNamespace;
    }

    /**
     * Task not found
     * 
     * @return string
     */
    public function classNotFound()
    {
        die('[Error]: The task command ' .$this->getNamespace(). ' not found.'."\n");
    }

    /**
     * Task method not found
     * 
     * @return string
     */
    public function methodNotFound()
    {
        die('[Error]: The method ' .$this->getMethod(). ' not found in '.$this->getClass()." task.\n");
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
     * Fetch the current routed class name
     *
     * @return string
     */
    public function getClass()
    {
        $class = self::ucwordsUnderscore($this->class);
        return $class;
    }

    /**
     * Returns to current method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Replace underscore to spaces to use ucwords
     * 
     * Before : widgets\tutorials a  
     * After  : Widgets\Tutorials_A
     * 
     * @param string $string namespace part
     * 
     * @return void
     */
    protected static function ucwordsUnderscore($string)
    {
        $str = str_replace('_', ' ', $string);
        $str = ucwords($str);
        return str_replace(' ', '_', $str);
    }

    /**
     * Get currently worked domain name
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->HOST;
    }

}