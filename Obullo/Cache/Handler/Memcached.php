<?php

namespace Obullo\Cache\Handler;

use ReflectionClass;
use RuntimeException;
use Obullo\Cache\CacheInterface;
    
/**
 * Memcached Caching Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Memcached implements CacheInterface
{
    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;
    
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
     * @param object $memcached \Memcached
     * @param object $params    params
     */
    public function __construct(\Memcached $memcached, array $params)
    {
        $this->memcached = $memcached; 
        $this->params    = $params;

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
        if (empty($this->params['nodes'][0]['host']) || empty($this->params['nodes'][0]['port'])) {  // If we have no slave servers
            return;
        }
        foreach ($this->params['nodes'] as $servers) {
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
    public function has($key)
    {
        if ($this->memcached->get($key)) {
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
    public function set($key, $data, $ttl = 60)
    {
        return $this->memcached->set($key, $data, time() + $ttl);
    }

    /**
     * Set keys
     * 
     * @param array   $data key - value
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
    public function remove($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Remove specified keys.
     * 
     * @param array $keys keys
     * 
     * @return void
     */
    public function removeItems(array $keys)
    {
        $this->memcached->deleteMulti($keys);
        return;
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
    public function replace($key, $data, $ttl = 60)
    {
        return $this->memcached->replace($key, $data, time() + $ttl);
    }

    /**
     * Replace keys
     * 
     * @param array   $data key - value
     * @param integer $ttl  ttl
     * 
     * @return boolean
     */
    public function replaceItems(array $data, $ttl)
    {
        return $this->setArray($data, $ttl);
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
     * Set Array
     * 
     * @param array $data cache data
     * @param int   $ttl  expiration time
     * 
     * @return void
     */
    protected function setArray(array $data, $ttl = 60)
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
     * Close the connection
     * 
     * @return void
     */
    public function close()
    {
        return;
    }

    /**
     * Flush all items in 1 seconds (default)
     * 
     * @return boolean
     */
    public function flushAll()
    {
        $this->memcached->flush(1);
        return $this->memcached->getResultCode();
    }
}