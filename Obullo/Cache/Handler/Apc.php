<?php

namespace Obullo\Cache\Handler;

use RuntimeException;
use Obullo\Cache\CacheInterface;
use Obullo\Config\ConfigInterface;

/**
 * Apc Caching Class
 *
 * @category  Cache
 * @package   Apc
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/cache
 */
class Apc implements CacheInterface
{
    const SERIALIZER_NONE = 'none';

    /**
     * Constructor
     * 
     * @param object $config \Obullo\Config\ConfigInterface
     */
    public function __construct(ConfigInterface $config)
    {
        $config = null;
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
     * Get cache data.
     * 
     * @param string $key cache key
     * 
     * @return object
     */
    public function get($key)
    {
        $value = apc_fetch($key);
        if (is_array($value) && isset($value[0])) {
            return $value = $value[0];
        }
        return $value;
    }

    /**
     * Verify if the specified key exists.
     * 
     * @param string $key cache key.
     * 
     * @return boolean true or false
     */
    public function exists($key)
    {
        return apc_exists($key);
    }

    /**
     * Set array
     * 
     * @param array $data cache data.
     * @param int   $ttl  expiration time.
     * 
     * @return boolean
     */
    public function setArray($data, $ttl)
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
     * Save
     * 
     * @param mix $key  cache key.
     * @param mix $data cache data.
     * @param int $ttl  expiration time
     * 
     * @return array
     */
    public function set($key, $data = 60, $ttl = 60) 
    {
        if (! is_array($key)) {
            return apc_store($key, array($data, time(), $ttl), $ttl);
        }
        return $this->setArray($key, $data);
    }

    /**
     * Remove specified key.
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function delete($key)
    {
        return apc_delete($key);
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
    public function replace($key, $data = 60, $ttl = 60) 
    {
        if (! is_array($key)) {
            $this->delete($key);
            return apc_store($key, array($data, time(), $ttl), $ttl);
        }
        return $this->setArray($key, $data);
    }

    /**
     * Clean all data
     * 
     * @param string $type clean type
     * 
     * @return object
     */
    public function flushAll($type = 'user')
    {
        return apc_clear_cache($type);
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
}