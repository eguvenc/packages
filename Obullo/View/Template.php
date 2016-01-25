<?php

namespace Obullo\View;

use Closure;
use Obullo\Http\Stream;
use Obullo\Http\Controller;
use Obullo\View\ViewInterface as View;
use Obullo\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;

/**
 * Temlate Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Template implements TemplateInterface
{
    /**
     * View
     * 
     * @var object
     */
    protected $view;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param object $container container
     * @param object $view      view
     * @param object $logger    logger
     */
    public function __construct(Container $container, View $view, Logger $logger)
    {
        $this->view = $view;
        $this->container = $container;
        
        $this->logger = $logger;
        $this->logger->debug('Template Class Initialized');
    }

    /**
     * Include template file from /resources/templates folder
     * 
     * @param string $filename name
     * @param array  $data     data
     * 
     * @return string
     */
    public function load($filename, $data = null)
    {
        return $this->view->getBody(TEMPLATES, $filename, $data, true);
    }

    /**
     * Get template files as string
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return object Stream
     */
    public function get($filename, $data = null)
    {
        return $this->view->getBody(TEMPLATES, $filename, $data, false);
    }

    /**
     * Make template files as Stream body
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return object Stream
     */
    public function make($filename, $data = null)
    {
        $html = $this->view->getBody(TEMPLATES, $filename, $data, false);
    
        return $this->body($html);
    }

    /**
     * Create http body
     * 
     * @param string $html output
     * 
     * @return object
     */
    public function body($html)
    {
        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($html);
        return $body;
    }

    /**
     * Set variables
     * 
     * @param mixed $key view key => data or combined array
     * @param mixed $val mixed
     * 
     * @return void
     */
    public function assign($key, $val = null)
    {
        $this->view->assign($key, $val);        
    }

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->container->get($key);
    }

}