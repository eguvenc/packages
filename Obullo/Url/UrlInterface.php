<?php

namespace Obullo\Url;

/**
 * Url Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UrlInterface
{
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
     * Get Assets URL
     * 
     * @param string $uri    asset uri
     * @param string $folder whether to add asset folder
     * 
     * @return string
     */
    public function assetsUrl($uri = '', $folder = true);

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
     * @param string $uriStr the URI string
     * 
     * @return string
     */
    public function siteUrl($uriStr = '');

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

    /**
     * Create static assets urls
     * 
     * @param string $uri      /images/example.png
     * @param mixed  $protocol http:// or https://
     * @param mixed  $url      dynamic url ( overrides to asset url in config )
     * 
     * @return string
     */
    public function asset($uri, $protocol = '', $url = '');
    
}