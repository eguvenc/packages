<?php

namespace Obullo\View;

use Closure;
use Obullo\Http\Stream;
use Obullo\Http\Controller;
use Obullo\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;

/**
 * View Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class View implements ViewInterface
{
    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params = array();

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Data
     * 
     * @var array
     */
    protected $data = array();

    /**
     * View folders
     * 
     * @var array
     */
    protected $folders = array();

    /**
     * Constructor
     * 
     * @param object $container container
     * @param object $logger    logger
     * @param array  $params    service provider parameters
     */
    public function __construct(Container $container, Logger $logger, array $params)
    {
        $this->container = $container;
        $this->params = $params;
        $this->logger = $logger;
        $this->logger->debug('View Class Initialized');
    }

    /**
     * Register view folder
     * 
     * @param string $name folder name
     * @param string $path folder path
     *
     * @return void
     */
    public function addFolder($name, $path = null)
    {
        $this->folders[$name] = $path;
    }

    /**
     * Check folders & returns to array if yes.
     *
     * @return boolean
     */
    public function hasFolders()
    {
        return (empty($this->folders)) ? false : $this->getFolders();
    }

    /**
     * Returns to folders
     * 
     * @return array
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Set variables
     * 
     * @param mixed $key key
     * @param mixed $val val
     * 
     * @return object
     */
    public function assign($key, $val = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data($k, $v);
            }
        } else {
            $this->data($key, $val);
        }
        return $this;
    }

    /**
     * Set variables
     * 
     * @param string $key view key data
     * @param mixed  $val mixed
     * 
     * @return object
     */
    protected function data($key, $val)
    {
        $this->data[$key] = $val;
        return $this;
    }

    /**
     * Include nested view files from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string                      
     */
    public function load($filename, $data = array())
    {
        return $this->renderNestedView($filename, $data, true);
    }

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = array())
    {
        return $this->renderNestedView($filename, $data, false);
    }

    /**
     * Get as stream
     * 
     * @param string $filename filename
     * @param array  $data     data
     * 
     * @return object stream
     */
    public function getStream($filename, $data = array())
    {
        $template = $this->get($filename, $data);
        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($template);
        return $body;
    }

    /**
     * Render nested view files
     * 
     * @param string  $filename filename
     * @param mixed   $data     array data
     * @param boolean $include  fetch as string or return
     * 
     * @return string                      
     */
    protected function renderNestedView($filename, $data = array(), $include = true)
    {
        /**
         * IMPORTANT:
         * 
         * Router may not available in some levels, forexample if we define a closure route 
         * which contains view class, it will not work if router not available in the controller.
         * So first we need check Controller is available if not we use container->router.
         */
        if (! class_exists('Obullo\Http\Controller', false) || Controller::$instance == null) {
            $router = $this->container->get('router');
        } else {
            $router = &Controller::$instance->router;  // Use nested controller router ( @see the Layer package. )
        }
        $path = $router->getModule('/') . $router->getDirectory();
        $path = (empty($path)) ? 'views' : $path;  // Read view file from /modules/views/ folder
        /**
         * End layer package support
         */
        $body = $this->render($filename, MODULES .$path .'/view/', $data);

        if ($include === false) {
            return $body;
        }
        $response = $this->container->get('response');
        $response->getBody()->write($body);
        return $response;
    }

    /**
     * Render view
     * 
     * @param string $filename filename
     * @param string $path     path
     * @param array  $data     data
     * 
     * @return string
     */
    public function render($filename, $path, $data = array())
    {
        $data = array_merge($this->data, $data);

        $engineClass = "\\".trim($this->params['engine'], '\\');
        $engine = new $engineClass($path);
        $engine->setContainer($this->container);

        if ($folders = $this->hasFolders()) {
            foreach ($folders as $name => $folder) {
                $engine->addFolder($name, $folder);
            }
        }
        return $engine->render($filename, $data);
    }

}