<?php

namespace Obullo\Cache\Handler;

use RuntimeException;
use Obullo\Cache\CacheInterface;

/**
 * Memcache Caching Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Memcache implements CacheInterface
{
    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Memcache object
     * 
     * @var object
     */
    protected $memcache;
    
    /**
     * Default memcache connection
     * 
     * @var array
     */
    protected $defaultConnection = array();  // Default connection array in config redis.php

    /**
     * Constructor
     * 
     * @param object $memcache \Memcache
     * @param array  $params   service params
     */
    public function __construct(\Memcache $memcache, array $params)
    {
        $this->memcache = $memcache;
        $this->params   = $params;

        $this->defaultConnection = $this->params['connections'][key($this->params['connections'])];
        $this->connect();
    }

    /**
     * Connect to Memcached
     * 
     * @return boolean
     */
    public function connect()
    {
        $this->openNodeConnections();
        return true;
    }

    /**
     * Connect to memcached nodes
     * 
     * @return void
     */
    protected function openNodeConnections()
    {
        if (! empty($this->params['nodes'][0]['host']) && ! empty($this->params['nodes'][0]['port'])) {
            array_unshift($this->params['nodes'], $this->defaultConnection);  // Add default connection to nodes
        } else {
            return;  // If there is no node.
        }
        $default = $this->defaultConnection['options'];

        foreach ($this->params['nodes'] as $servers) {
            if (empty($servers['host']) || empty($servers['port'])) {
                throw new RuntimeException(
                    sprintf(
                        ' %s node configuration error, host or port can\'t be empty.',
                        get_class()
                    )
                );
            }
            $this->memcache->addServer($servers['host'], $servers['port'], $default['persistent'], $servers['weight'], $default['timeout'], $default['attempt']);
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
        return $serializer;
    }

    /**
     * Get client option.
     * http://www.php.net/manual/en/memcached.constants.php
     * 
     * @param string $option option constant
     * 
     * @return bool false
     */
    public function getOption($option = 'OPT_SERIALIZER')
    {
        return $option = false;
    }

    /**
     * Set option
     * 
     * @param string $option constant name
     * @param string $value  constant value name
     *
     * @return void
     */
    public function setOption($option = 'OPT_SERIALIZER', $value = 'SERIALIZER_PHP')
    {
        $option = $value = false;
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
        $value = $this->memcache->get($key, false);
        if (is_array($value) && isset($value[0])) {
            $value = $value[0];
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
        if ($this->memcache->get($key, false)) {
            return true;
        }
        return false;
    }

    /**
     * Set Array
     * 
     * @param array $data cache data
     * @param int   $ttl  expiration time
     * 
     * @return void
     */
    public function setArray($data, $ttl = 60)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->memcache->set($k, $v, 0, $ttl);
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
     * @return boolean
     */
    public function set($key, $data = 60, $ttl = 60)
    {
        if (! is_array($key)) {
            return $this->memcache->set($key, array($data, time(), $ttl), 0, $ttl);
        }
        return $this->setArray($key, $data);
    }

    /**
     * Remove specified keys.
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function delete($key)
    {
        return $this->memcache->delete($key);
    }

    /**
     * Replace key value
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
            $this->memcache->replace($key, array($data, time(), $ttl), 0, $ttl);
        }
        return $this->setArray($key, $data);
    }

    /**
     * Remove all keys and data from the cache.
     * 
     * @return boolean
     */
    public function flushAll()
    {
        return $this->memcache->flush();
    }

    /**
     * Get software information installed on your server.
     * 
     * @return object
     */
    public function info()
    {
        return $this->memcache->getStats();
    }

    /**
     * Get Meta Data
     * 
     * @param string $key cache key.
     * 
     * @return object
     */
    public function getMetaData($key)
    {
        $stored = $this->memcache->get($key);
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
     * Close the connection
     * 
     * @return void
     */
    public function close()
    {
        $this->memcache->close();
        return;
    }

}