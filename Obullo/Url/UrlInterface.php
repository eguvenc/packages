<?php

namespace Obullo\Url;

/**
 * Url Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UrlInterface
{
    /**
     * Create link with http protocol
     * 
     * @param string $protocol http protocol
     * 
     * @return void
     */
    public function withProtocol($protocol = null);

    /**
     * Create asset with an external url
     * 
     * @param string $url url
     * 
     * @return object
     */
    public function withUrl($url = null);

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
    public function anchor($uri = '', $title = '', $attributes = '');

    /**
     * Create static assets urls
     * 
     * @param string $uri /images/example.png
     * 
     * @return string
     */
    public function asset($uri);

    /**
     * Get Base URL definition
     * 
     * @param string $uri custom uri
     * 
     * @return string
     */
    public function baseUrl($uri = '');

    /**
     * Site URL
     *
     * @param string $uri the URI string
     * 
     * @return string
     */
    public function siteUrl($uri = '');

    /**
     * Get current url
     *
     * @return string
     */
    public function currentUrl();

    /**
     * Get webhost definition
     *
     * @return string
     */
    public function webhost();    
}