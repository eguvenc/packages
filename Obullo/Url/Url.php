<?php

namespace Obullo\Url;

use Interop\Container\ContainerInterface as Container;
use Obullo\Log\LoggerInterface as Logger;

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
    protected $url;

    /**
     * Parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Request
     * 
     * @var request
     */
    protected $request;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Url scheme
     * 
     * @var boolean
     */
    protected $scheme;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param ContainerInterface $container container
     * @param LoggerInterface    $logger    logger
     * @param array              $params    service parameters
     */
    public function __construct(Container $container, Logger $logger, array $params)
    {
        $this->logger = $logger;
        $this->params = $params;
        $this->request = $container->get('app')->request;  // Assign global request object
        $this->container = $container;

        $this->logger->debug('Url Class Initialized');
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($data = null)
    {
        $this->url = new \Obullo\Http\Uri;

        if (empty($data)) {
            $data = $this->request->getUri()->getHost();
        }
        $this->url = $this->url->withHost($data);
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
     * Returns to last created url string
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns to url string
     * 
     * @return string
     */
    public function __toString()
    {
        if (! empty($this->url)) {
            return $this->getUriString();
        }
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
     * @param bool   $baseUrl    whether to use base url
     * 
     * @return string
     */
    public function anchor($uri = '', $label = '', $attributes = '', $baseUrl = true)
    {
        if ($baseUrl) {
            $uri = $this->getBaseUrl($uri);
        }
        if (empty($label)) {
            $label = $uri;
        }
        $attributes = ($attributes != '') ? self::parseAttributes($attributes) : '';
        $anchor = '<a href="' .$uri . '"' . $attributes . '>' . (string)$label . '</a>';
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
    public function withAnchor($label = '', $attributes = '')
    {
        return $this->anchor($this->getUriString(), $label, $attributes, false);
    }

    /**
     * Creates an asset url based on the "external" URL.
     *
     * @param string $path uri path
     * 
     * @return string
     */
    public function withAsset($path = '')
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
     * Get Base URL
     * 
     * @param string $uri custom uri
     * 
     * @return string
     */
    public function getBaseUrl($uri = '')
    {
        return rtrim($this->params['baseurl'], '/') .'/'. ltrim($uri, '/');
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->getBaseUrl($this->request->getUri()->getRequestUri());
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