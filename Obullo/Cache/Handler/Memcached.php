<?php

namespace Obullo\Cache\Handler;

use ReflectionClass;
use RuntimeException;
use Obullo\Cache\CacheInterface;
use Obullo\Config\ConfigInterface;
    
/**
 * Memcached Caching Class
 *
 * @category  Cache
 * @package   Memcached
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/cache
 */
class Memcached implements CacheInterface
{
    /**
     * Memcached client
     * 
     * @var object
     */
    protected $memcached;

    /**
     * Available serializers
     * 
     * @var array
     */
    public $serializers = array(
        0 => 'none',
        1 => 'php',         // Memcached::SERIALIZER_PHP
        2 => 'igbinary',    // Memcached::SERIALIZER_IGBINARY
        3 => 'json',        // Memcached::SERIALIZER_JSON
    );
    
    /**
     * Constructor
     * 
     * @param object $config    \Obullo\Config\ConfigInterface
     * @param object $memcached \Memcached
     */
    public function __construct(ConfigInterface $config, \Memcached $memcached)
    {
        $this->memcached = $memcached; 
        $this->config = $config->load('cache/memcached');

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
        if (empty($this->config['nodes'][0]['host']) || empty($this->config['nodes'][0]['port'])) {  // If we have no slave servers
            return;
        }
        foreach ($this->config['nodes'] as $servers) {
            if (empty($servers['host']) || empty($servers['port'])) {
                throw new RuntimeException(
                    sprintf(
                        ' %s node configuration error, host or port can\'t be empty.',
                        get_class()
                    )
                );
            }
            $this->memcached->addServer($servers['host'], $servers['port'], $servers['weight']);
        }
    }

    /**
     * If method does not exist in this class call it from $this->memcached
     * 
     * @param string $method    methodname
     * @param array  $arguments method arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->memcached, $method), $arguments);
    }

    /**
     * Get current serializer name
     * 
     * @return string serializer name
     */
    public function getSerializer()
    {
        $number = $this->getOption('OPT_SERIALIZER');

        return $this->serializers[$number];
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
        $this->memcached->setOption(\Memcached::OPT_SERIALIZER, $this->serializers[$serializer]);
    }

    /**
     * Get client option.
     * http://www.php.net/manual/en/memcached.constants.php
     * 
     * @param string $option option constant
     * 
     * @return string value
     */
    public function getOption($option = 'OPT_SERIALIZER')
    {
        $obj = new ReflectionClass('Memcached');
        $constant = $obj->getconstant($option);
        return $this->memcached->getOption($constant);
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
        $obj    = new ReflectionClass('Memcached');
        $option = $obj->getconstant($option);
        $value  = $obj->getconstant($value);

        $this->redis->setOption($option, $value); 
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
        return $this->memcached->get($key);
    }

    /**
     * Returns the keys that match a certain pattern.
     * 
     * @return array the keys that match a certain pattern.
     */
    public function getAllKeys()
    {
        return $this->memcached->getAllKeys();
    }

    /**
     * Get All Data
     * 
     * @return array return all the key and data
     */
    public function getAllData()
    {
        return $this->memcached->fetchAll();
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
        if ($this->memcached->get($key)) {
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
            foreach ($data as $key => $value) {
                $this->memcached->set($key, $value, time() + $ttl);
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
            return $this->memcached->set($key, $data, time() + $ttl);
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
        if (is_array($key)) {
            return $this->memcached->deleteMulti($key);
        }
        return $this->memcached->delete($key);
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
            $this->memcached->replace($key, $data, time() + $ttl);
        }
        return $this->setArray($key, $data);
    }

    /**
     * Flush all items in 1 seconds (default)
     * 
     * @param int $expiration expiration time
     * 
     * @return boolean
     */
    public function flushAll($expiration = 1)
    {
        $this->memcached->flush($expiration);
        return $this->memcached->getResultCode();
    }

    /**
     * Get software information installed on your server.
     * 
     * @return object
     */
    public function info()
    {
        return $this->memcached->getStats();
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
        $stored = $this->memcached->get($key);
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
        return;
    }

}