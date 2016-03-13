<?php

namespace Obullo\Cache\Handler;

use RuntimeException;
use Obullo\Cache\CacheInterface;

/**
 * Apc Caching Class
 *
 * @license http://opensource.org/licenses/MIT MIT license
 * @link    http://obullo.com/package/cache
 */
class Apc implements CacheInterface
{
    const SERIALIZER_NONE = 'none';

    /**
     * Constructor
     */
    public function __construct()
    {
        if (! extension_loaded('apc') || ini_get('apc.enabled') != '1') {
            throw new RuntimeException(
                sprintf(
                    ' %s driver is not installed.', get_class()
                )
            );
        }
    }

    /**
     * Get current serializer name
     * 
     * @return string serializer name
     */
    public function getSerializer()
    {
        return null;
    }

    /**
     * Sets serializer
     * 
     * @param string $serializer type
     *
     * @return void
     */
    public function setSerializer($serializer = 'php')
    {
        return $serializer = null;
    }

    /**
     * Get item.
     * 
     * @param string $key cache key
     * 
     * @return object
     */
    public function getItem($key)
    {
        $value = apc_fetch($key);
        if (is_array($value) && isset($value[0])) {
            return $value = $value[0];
        }
        return $value;
    }

    /**
     * Get multiple items.
     * 
     * @param array $keys cache keys
     * 
     * @return array
     */
    public function getItems(array $keys)
    {
        $items = array();
        foreach ($keys as $key) {
            $items[] = $this->getItem($key);
        }
        return $items;
    }

    /**
     * Verify if the specified key exists.
     * 
     * @param string $key cache key.
     * 
     * @return boolean true or false
     */
    public function hasItem($key)
    {
        return apc_exists($key);
    }

    /**
     * Save
     * 
     * @param mix $key  cache key.
     * @param mix $data cache data.
     * @param int $ttl  expiration time
     * 
     * @return array
     */
    public function setItem($key, $data, $ttl = 60) 
    {
        return apc_store($key, array($data, time(), $ttl), $ttl);
    }

    /**
     * Set items
     * 
     * @param array   $data data
     * @param integer $ttl  ttl
     *
     * @return boolean
     */
    public function setItems(array $data, $ttl = 60)
    {
        return $this->setArray($data, $ttl);
    }

    /**
     * Remove specified key.
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function removeItem($key)
    {
        return apc_delete($key);
    }

    /**
     * Remove specified keys.
     * 
     * @param array $keys keys
     * 
     * @return boolean
     */
    public function removeItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }
        return;
    }

    /**
     * Replace data
     * 
     * @param mix $key  cache key.
     * @param mix $data cache data.
     * @param int $ttl  expiration time
     * 
     * @return boolean
     */
    public function replaceItem($key, $data, $ttl = 60) 
    {
        $this->remove($key);
        return apc_store($key, array($data, time(), $ttl), $ttl);
    }

     /**
     * Replace data
     * 
     * @param array   $data key - value
     * @param integer $ttl  ttl
     * 
     * @return boolean
     */
    public function replaceItems(array $data, $ttl = 60)
    {
        return $this->setArray($data, $ttl);
    }

    /**
     * Cache Info
     * 
     * @param string $type info type
     * 
     * Types:
     *     "user"
     *     "filehits"
     * 
     * @return array
     */
    public function info($type = null)
    {
        return apc_cache_info($type);
    }

    /**
     * Get meta data
     * 
     * @param string $key cache key.
     * 
     * @return array
     */
    public function getMetaData($key)
    {
        $stored = apc_fetch($key);
        if (count($stored) !== 3) {
            return false;
        }
        list($data, $time, $ttl) = $stored;
        return array(
            'expire' => $time + $ttl,
            'mtime'  => $time,
            'data'   => $data
        );
    }

    /**
     * Set array
     * 
     * @param array $data cache data.
     * @param int   $ttl  expiration time.
     * 
     * @return boolean
     */
    protected function setArray($data, $ttl)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->set($k, $v, $ttl);
            }
            return true;
        }
        return false;
    }

    /**
     * Connect
     * 
     * @return void
     */
    public function connect()
    {
        return;
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    public function close()
    {
        return;
    }

    /**
     * Clean all data
     * 
     * @return object
     */
    public function flushAll()
    {
        return apc_clear_cache('user');
    }

}