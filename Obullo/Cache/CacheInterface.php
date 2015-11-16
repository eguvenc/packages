<?php

namespace Obullo\Cache;

/**
 * Cache Handler Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface CacheInterface
{
    /**
     * Connection the cache..
     * 
     * @return boolean
     */
    public function connect();

    /**
     * Verify if the specified key exists.
     * 
     * @param string $key cache key.
     * 
     * @return boolean true or false
     */
    public function exists($key);

    /**
     * Set cache data.
     *
     * @param mix $key  cache key
     * @param mix $data cache data
     * @param int $ttl  expiration time
     * 
     * @return boolean
     */
    public function set($key, $data = 60, $ttl = 60);

    /**
     * Get cache data.
     *
     * @param string $key cache key
     * 
     * @return mix
     */
    public function get($key);

    /**
     * Replace cache data
     * 
     * @param mix $key  cache key
     * @param mix $data cache data
     * @param int $ttl  expiration time
     * 
     * @return boolean
     */
    public function replace($key, $data = 60, $ttl = 60);

    /**
     * Remove specified keys.
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function delete($key);

}