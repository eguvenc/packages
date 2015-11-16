<?php

namespace Obullo\Layer;

use Obullo\Http\ServerRequestFactory;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use Obullo\Container\ContainerInterface as Container;

/**
 * Layer Request
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Request
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Config parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Get backup of super globals
     * 
     * @var array
     */
    protected $globals;

    /**
     * Constructor
     *
     * @param object $c      ContainerInterface
     * @param object $logger LoggerInterface
     * @param array  $params service parameters
     */
    public function __construct(Container $c, Logger $logger, array $params)
    {   
        $this->c = $c;
        $this->logger = $logger;
        $this->params = $params;
        
        $this->createEnvironment();
    }

    /**
     * Clone http super globals
     * 
     * @return void
     */
    protected function createEnvironment()
    {
        /**
         * Backup original super globals
         */
        $this->globals['_SERVER'] = &$_SERVER;
        $this->globals['_GET'] = &$_GET;
        $this->globals['_POST'] = &$_POST;
    }

    /**
     * Create new request
     * 
     * @param string $uri request uri
     * 
     * @return object
     */
    protected function createRequest($uri)
    {
        $_SERVER = $_GET = $_POST = array();

        $_SERVER['LAYER_REQUEST'] = true;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SCRIPT_NAME'] = 'index.php';
        $_SERVER['QUERY_STRING'] = '';

        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST
        );
    }

    /**
     * Layers GET Request
     * 
     * @param string  $uri        uri string
     * @param array   $data       get data
     * @param integer $expiration cache ttl
     * 
     * @return string
     */
    public function get($uri = '/', $data = array(), $expiration = '')
    {
        if (is_numeric($data)) { // Set expiration as second param if data not provided
            $expiration = $data;
            $data = array();
        }
        return $this->newRequest('GET', $uri, $data, $expiration);
    }

    /**
     * Layers POST Request
     * 
     * @param string  $uri        uri string
     * @param array   $data       post data
     * @param integer $expiration cache ttl
     * 
     * @return string
     */
    public function post($uri = '/', $data = array(), $expiration = '')
    {
        if (is_numeric($data)) {  // Set expiration as second param if data not provided
            $expiration = $data;
            $data = array();
        }
        return $this->newRequest('POST', $uri, $data, $expiration);
    }

    /**
     * Create new request
     * 
     * Layer always must create new instance other ways we can't use nested layers.
     * 
     * @param string  $method     request method
     * @param string  $uri        uri string
     * @param array   $data       request data
     * @param integer $expiration ttl
     * 
     * @return string
     */
    public function newRequest($method, $uri = '/', $data = array(), $expiration = '')
    {
        $layer = new Layer(
            $this->c,
            $this->params,
            $this->globals
        );
        $layer->clear();
        $layer->newRequest(
            $this->createRequest($uri),
            $uri,
            $method,
            $data
        );

        $id = $layer->getId();

        /**
         * Dispatch route errors
         */
        if ($layer->getError() != '') {
            $error = $layer->getError();
            $layer->restore();
            return Error::getError($error);
        }
        /**
         * Cache support
         */
        if ($this->params['cache'] && $response = $this->c['cache']->get($id)) {   
            $layer->restore();
            return base64_decode($response);
        }

        $response = $layer->execute(); // Execute the process

        /**
         * Cache support
         */
        if (is_numeric($expiration)) {
            $this->c['cache']->set($id, base64_encode($response), (int)$expiration); // Write to Cache
        }
        $layer->restore();  // Restore controller objects

        if (is_array($response) && isset($response['error'])) {
            return Error::getError($response);  // Error template support
        }
        
        if ($this->params['log']) {
            $this->log($uri, $id, $response);
        }

        return (string)$response;
    }

    /**
     * Call helpers ( flush class .. ) $this->c['layer']->flush('views/header');
     * 
     * @param string $uri  string
     * @param array  $data params
     * 
     * @return boolean
     */
    public function flush($uri, $data = array())
    {
        $flush = new Flush($this->logger, $this->c['cache']);

        return $flush->uri($uri, $data);
    }

    /**
     * Log response data
     * 
     * @param string $uri      uri string
     * @param string $id       layer id
     * @param string $response data
     * 
     * @return void
     */
    protected function log($uri, $id, $response)
    {
        $uriString = md5($this->c['app']->request->getUri()->getPath());

        $this->logger->debug(
            '$_LAYER: '.strtolower($uri), 
            array(
                'id' => $id, 
                'output' => '<div class="obullo-layer" data-unique="u'.uniqid().'" data-id="'.$id.'" data-uristring="'.$uriString.'">' .$response. '</div>',
            )
        );
    }

    
}