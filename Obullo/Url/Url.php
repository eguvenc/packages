<?php

namespace Obullo\Url;

use Psr\Http\Message\UriInterface as Uri;
use Psr\Http\Message\RequestInterface as Request;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use League\Container\ContainerInterface as Container;

/**
 * Url Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Url
{
    /**
     * Url
     * 
     * @var string
     */
    protected $url = '';

    /**
     * Url scheme
     * 
     * @var boolean
     */
    protected $scheme;

    /**
     * Constructor
     * 
     * @param ContainerInterface $container container
     * @param RequestInterface   $request   request
     * @param LoggerInterface    $logger    logger
     * @param array              $params    service parameters
     */
    public function __construct(Container $container, Request $request, Logger $logger, array $params)
    {
        $this->logger = $logger;
        $this->params = $params;
        $this->request = $request;
        $this->container = $container;
        $this->uri = $request->getUri();

        $this->logger->debug('Url Class Initialized');
    }

    /**
     * Create new url
     * 
     * @param string $url url
     * 
     * @return object
     */
    public function createUrl($url = '')
    {
        $this->url = new \Obullo\Http\Uri;

        if (empty($url)) {
            $url = $this->_getSiteUrl($url);
        }
        $this->url = $this->url->withHost($url);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($data)
    {
        $this->scheme = true;
        $this->url = $this->url->withScheme($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $this->url = $this->url->withUserInfo($user, $password);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($data = null)
    {
        if (empty($data)) {
            $data = $this->_getSiteUrl($data);
        }
        $this->url = $this->url->withHost($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($data)
    {
        $this->url = $this->url->withPort($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($data)
    {
        $this->url = $this->url->withPath($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($data)
    {
        $this->url = $this->url->withQuery($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->url->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        return $this->url->getUserInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->url->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->url->getPort();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->url->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->url->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->url->getFragment();
    }

    /**
     * Returns to last created url string
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns to last created url string
     * 
     * @return string
     */
    public function getUriString()
    {
        return $this->url->__toString();
    }

    /**
     * Creates an anchor based on the "local" URL.
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
     * Creates an anchor based on the "external" URL.
     *
     * @param string $label      the link name
     * @param mixed  $attributes any attributes
     * 
     * @return string
     */
    public function makeAnchor($label = '', $attributes = '')
    {
        return $this->anchor($this->getUriString(), $label, $attributes);
    }

    /**
     * Creates an asset url based on the "external" URL.
     *
     * @param string $path uri path
     * 
     * @return string
     */
    public function makeAsset($path = '')
    {
        return $this->getUriString().$this->asset($path, true);
    }

    /**
     * Clear
     * 
     * @return void
     */
    protected function clear()
    {
        $this->scheme = null;
    }

    /**
     * Creates asset urls e.g. /images/example.png
     * 
     * @param string $uri            asset uri
     * @param string $disableBaseUrl whether use base asset url
     * 
     * @return string
     */
    public function asset($uri, $disableBaseUrl = false)
    {
        $url = '';
        if ($disableBaseUrl == false) {
            $url = $this->params['assets']['url'];
            if ($this->params['assets']['url'] != '' && $this->params['assets']['url'] != '/') {
                $url = $this->prep($this->params['assets']['url']); 
            }
        }
        $url = rtrim($url, '/').'/';
        $uri = $url.trim($this->params['assets']['folder'], '/').'/'.ltrim($uri, '/');

        if (! empty($this->scheme)) {
            $uri = preg_replace('#^https?:\/\/#i', '', $uri);
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
        if ($baseUrl == '/') {
            $baseUrl = '';
        } elseif (! empty($this->scheme)) {
            $baseUrl = preg_replace('#^https?:\/\/#i', '', $baseUrl);
        }
        if ($uri == '') {
            return $baseUrl;
        } 
        $url = $baseUrl. trim($uri, '/');
        return $url;
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function currentUrl()
    {
        return $this->siteUrl($this->request->getUri()->getRequestUri());
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