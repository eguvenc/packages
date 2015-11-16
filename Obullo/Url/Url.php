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
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param string $uri        the URL
     * @param string $title      the link title
     * @param mixed  $attributes any attributes
     * 
     * @return string
     */
    public function anchor($uri = '', $title = '', $attributes = '')
    {
        $siteUrl = $this->_getSiteUrl($uri);
        
        if (empty($title)) {
            $title = $siteUrl;
        }
        $attributes = ($attributes != '') ? self::parseAttributes($attributes) : '';

        return '<a href="' . $siteUrl . '"' . $attributes . '>' . (string)$title . '</a>';
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
     * Get Assets URL
     * 
     * @param string $uri    asset uri
     * @param string $folder whether to add asset folder
     * 
     * @return string
     */
    public function assetsUrl($uri = '', $folder = true)
    {
        $assetsFolder = ($folder) ? trim($this->params['assets']['folder'], '/').'/' : '';
        return $this->params['assets']['url'] . $assetsFolder . ltrim($uri, '/');
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
        return rtrim($this->params['baseurl'], '/') .'/'. ltrim($uri, '/');
    }

    /**
     * Site URL
     *
     * @param string $uriStr the URI string
     * 
     * @return string
     */
    public function siteUrl($uriStr = '')
    {
        if (is_array($uriStr)) {
            $uriStr = implode('/', $uriStr);
        }
        if ($uriStr == '') {
            return $this->baseUrl() . $this->params['rewrite']['index.php'];
        } 
        return $this->baseUrl() . $this->params['rewrite']['index.php'] . trim($uriStr, '/');
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
     * Get current url
     *
     * @return string
     */
    public function webhost()
    {
        return trim($this->params['webhost'], '/');
    }

    /**
     * Create static assets urls
     * 
     * @param string $uri      /images/example.png
     * @param mixed  $protocol http:// or https://
     * @param mixed  $url      dynamic url ( overrides to asset url in config )
     * 
     * @return string
     */
    public function asset($uri, $protocol = '', $url = '')
    {
        $url = empty($url) ? $this->params['assets']['url'] : $url;
        $uri = $url.trim($this->params['assets']['folder'], '/').'/'.ltrim($uri, '/');

        if ($protocol == false) {
            $uri = preg_replace('#^https?:\/\/#i', '', $uri);
            $protocol = '';
        }
        if ($protocol == true) {  // Auto detect
            $protocol = ($this->request->isSecure()) ? 'https://' : 'http://';
        }
        if (! empty($protocol) || is_bool($protocol)) {
            $uri = preg_replace('#^https?:\/\/#i', '', $uri);
        }
        return $protocol.$uri;
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
     * @param string $str the URL
     * 
     * @return string
     */
    public function prep($str = '')
    {
        if ($str == 'http://' || $str == '') {
            return '';
        }
        if (! parse_url($str, PHP_URL_SCHEME)) {
            $str = 'http://' . $str;
        }
        return $str;
    }

}