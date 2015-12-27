<?php

namespace Obullo\Url;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use Obullo\Container\ContainerInterface as Container;

use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Message\RequestInterface as Request;

/**
 * Url Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Url implements UrlInterface
{
    /**
     * Uri
     * 
     * @var object
     */
    protected $uri;

    /**
     * Request
     * 
     * @var object
     */
    protected $request;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Service Parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Url protocol
     * 
     * @var string
     */
    protected $protocol = '';

    /**
     * Url
     * 
     * @var string
     */
    protected $url = '';

    /**
     * Constructor
     * 
     * @param RequestInterface $request request
     * @param LoggerInterface  $logger  logger
     * @param array            $params  service parameters
     */
    public function __construct(Request $request, Logger $logger, array $params)
    {
        $this->request = $request;
        $this->uri = $request->getUri();
        $this->params = $params;
        $this->logger = $logger;

        $this->logger->debug('Url Class Initialized');
    }

    /**
     * Create link with http protocol
     * 
     * @param string $protocol http protocol
     * 
     * @return object
     */
    public function withProtocol($protocol = null)
    {
        if ($protocol == null) {
            $protocol = ($this->request->isSecure()) ? 'https://' : 'http://';
        }
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Create asset with an external url
     * 
     * @param string $url url
     * 
     * @return object
     */
    public function withUrl($url = null)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param string $uri        the URL
     * @param string $label      the link name
     * @param mixed  $attributes any attributes
     * 
     * @return string
     */
    public function anchor($uri = '', $label = '', $attributes = '')
    {
        $siteUrl = $this->_getSiteUrl($uri);
        
        if (empty($label)) {
            $label = $siteUrl;
        }
        $attributes = ($attributes != '') ? self::parseAttributes($attributes) : '';
        $anchor = '<a href="' .$siteUrl . '"' . $attributes . '>' . (string)$label . '</a>';
        $this->clear();

        return $anchor;        
    }
    
    /**
     * Clear variables
     * 
     * @return void
     */
    protected function clear()
    {
        $this->protocol = '';
        $this->url = '';
    }

    /**
     * Create static assets urls
     * 
     * @param string $uri /images/example.png
     * 
     * @return string
     */
    public function asset($uri)
    {
        if (! empty($this->url)) {
            $url = $this->prep($this->url);
        } else {
            $url = $this->prep($this->params['assets']['url']);
        }
        $url = rtrim($url, '/').'/';
        $uri = $url.trim($this->params['assets']['folder'], '/').'/'.ltrim($uri, '/');

        if (! empty($this->protocol)) {
            $uri = $this->protocol.preg_replace('#^https?:\/\/#i', '', $uri);
        }
        $this->clear();
        return $uri;
    }

    /**
     * Get site url
     * 
     * @param string $uri uri
     * 
     * @return string site url
     */
    private function _getSiteUrl($uri)
    {
        $siteUri = $this->siteUrl($uri);

        return ( ! preg_match('!^\w+://! i', $uri)) ? $siteUri : $uri;
    }

    /**
     * Get Base URL
     * 
     * @param string $uri custom uri
     * 
     * @return string
     */
    public function baseUrl($uri = '')
    {
        $baseUrl = rtrim($this->params['baseurl'], '/') .'/'. ltrim($uri, '/');

        if ($baseUrl != '' && $baseUrl != '/') {
            $baseUrl = $this->prep($baseUrl);
        }
        return $baseUrl;
    }

    /**
     * Site URL
     *
     * @param string $uri the URI string
     * 
     * @return string
     */
    public function siteUrl($uri = '')
    {
        $baseUrl = $this->baseUrl();

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }
        if ($this->protocol != '') {
            if ($baseUrl == '/') {
                $baseUrl = '';
            } else {
                $baseUrl = preg_replace('#^https?:\/\/#i', '', $baseUrl);
            }
        }
        if ($uri == '') {
            $this->clear();
            return $baseUrl;
        } 
        $url = $this->protocol.$baseUrl. trim($uri, '/');
        $this->clear();
        return $url;
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function currentUrl()
    {
        return $this->siteUrl($this->uri->getRequestUri());
    }

    /**
     * Parse out the attributes
     *
     * Some of the functions use this
     *
     * @param array $attributes atributes
     * @param bool  $javascript javascript attributes
     * 
     * @return string
     */
    protected static function parseAttributes($attributes, $javascript = false)
    {
        if (is_string($attributes)) {
            return ($attributes != '') ? ' ' . $attributes : '';
        }
        $att = '';
        foreach ($attributes as $key => $val) {
            if ($javascript == true) {
                $att .= $key . '=' . $val . ',';
            } else {
                $att .= ' ' . $key . '="' . $val . '"';
            }
        }
        if ($javascript == true && $att != '') {
            $att = substr($att, 0, -1);
        }
        return $att;
    }
    
    /**
     * Prep URL
     *
     * Simply adds the http:// part if missing
     *
     * @param string $url the URL
     * 
     * @return string
     */
    public function prep($url = '')
    {
        if ($url == 'http://' || $url == '') {
            return '';
        }
        if (! parse_url($url, PHP_URL_SCHEME)) {
            $url = 'http://' . $url;
        }
        return $url;
    }

}