<?php

namespace Obullo\View;

use Closure;
use Obullo\Http\Stream;
use Obullo\Http\Controller;
use Obullo\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\StreamInterface;

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
     * Stream
     * 
     * @var object
     */
    protected $stream;

    /**
     * Stream template
     * 
     * @var string
     */
    protected $template;

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
     * Service parameters
     * 
     * @var array
     */
    protected $params = array();

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
    public function getFolders()
    {
        return (empty($this->folders)) ? false : $this->folders;
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
    public function get($filename = null, $data = array())
    {
        $template = $this->template;
        if ($filename != null) {
            $template = $this->renderNestedView($filename, $data, false);
        }
        if (is_object($this->stream) && $this->stream instanceof StreamInterface) {

            if (false == $this->stream->getContents()) {  // Write if content empty.
                $this->stream->write($template);
            }
            $body = $this->stream;
            $this->template = $this->stream = null;
            return $body;
        }
        return $template;
    }

    /**
     * Set variables
     * 
     * @param mixed $key key
     * @param mixed $val val
     * 
     * @return object
     */
    public function withData($key, $val)
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
     * With http stream
     *
     * @param object $stream stream
     * 
     * @return object stream
     */
    public function withStream($stream = null)
    {
        if (is_object($stream)) {
            $this->stream = $stream;
        } else {
            $this->stream = new Stream(fopen('php://temp', 'r+'));
        }
        if (is_string($stream)) {
            $this->template = $stream;
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

        $folder = (empty($path)) ? MODULES .'views' : MODULES .$path .'/view';

        /**
         * End layer package support
         */
        $body = $this->render($filename, $folder, $data);

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

        if ($folders = $this->getFolders()) {
            foreach ($folders as $name => $folder) {
                $engine->addFolder($name, $folder);
            }
        }
        return $engine->render($filename, $data);
    }

}