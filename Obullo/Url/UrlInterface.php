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
     * Create url
     * 
     * @param string $url url
     * 
     * @return object
     */
    public function createUrl($url);

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
     * Creates an anchor based on the "external" URL.
     *
     * @param string $label      the link name
     * @param mixed  $attributes any attributes
     * 
     * @return string
     */
    public function makeAnchor($label = '', $attributes = '');

    /**
     * Creates an asset url based on the "external" URL.
     *
     * Creates an anchor based on the local URL.
     * 
     * @return string
     */
    public function makeAsset();

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
     * Prep URL
     *
     * Simply adds the http:// part if missing
     *
     * @param string $uri the URL
     * 
     * @return string
     */
    public function prep($uri = '');
  
}