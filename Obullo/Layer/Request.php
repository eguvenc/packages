<?php

namespace Obullo\Layer;

use Obullo\Http\ServerRequestFactory;

use Psr\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use Interop\Container\ContainerInterface as Container;

/**
 * Layer Request
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Request
{
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
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     *
     * @param object $container ContainerInterface
     * @param object $logger    LoggerInterface
     * @param array  $params    service parameters
     */
    public function __construct(Container $container, Logger $logger, array $params)
    {   
        $this->container = $container;
        $this->logger = $logger;
        $this->params = $params;
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

        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST
        );
        $request->setContainer($this->container);
        return $request;
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
            $this->container,
            $this->params
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
        if ($this->params['cache'] && $response = $this->container->get('cache')->get($id)) {   
            $layer->restore();
            return base64_decode($response);
        }

        $response = $layer->execute(); // Execute the process

        /**
         * Cache support
         */
        if (is_numeric($expiration)) {
            $this->container->get('cache')->set($id, base64_encode($response), (int)$expiration); // Write to Cache
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
        $flush = new Flush(
            $this->logger,
            $this->container->get('cache')
        );
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
        $uriString = md5($this->container->get('app')->request->getUri()->getPath());

        $this->logger->debug(
            'Layer: '.strtolower($uri), 
            array(
                'id' => $id, 
                'output' => '<div class="obullo-layer" data-unique="u'.uniqid().'" data-id="'.$id.'" data-uristring="'.$uriString.'">' .$response. '</div>',
            )
        );
    }

    
}