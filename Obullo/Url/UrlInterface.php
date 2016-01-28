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
     * {@inheritdoc}
     */
    public function withHost($data = null);

    /**
     * {@inheritdoc}
     */
    public function withScheme($data);

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null);

    /**
     * {@inheritdoc}
     */
    public function withPort($data);

    /**
     * {@inheritdoc}
     */
    public function withPath($data);

    /**
     * {@inheritdoc}
     */
    public function withQuery($data);

    /**
     * Returns to last created url string
     * 
     * @return string
     */
    public function getUriString();

    /**
     * Returns to last created url string
     * 
     * @return string
     */
    public function getUrl();

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
    public function withAnchor($label = '', $attributes = '');

    /**
     * Creates an asset url based on the "external" URL.
     *
     * Creates an anchor based on the local URL.
     * 
     * @return string
     */
    public function withAsset();

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
    public function getBaseUrl($uri = '');

    /**
     * Get current url
     *
     * @return string
     */
    public function getCurrentUrl();

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