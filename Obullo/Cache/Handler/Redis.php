<?php

namespace Obullo\Cache\Handler;

use ReflectionClass;
use RuntimeException;
use Obullo\Cache\CacheInterface;

/**
 * Redis Caching Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Redis implements CacheInterface
{
    /**
     * Php redis client
     * 
     * @var object
     */
    protected $redis;

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Available serializers
     * 
     * @var array
     */
    public $serializers = array(
        'none' => \Redis::SERIALIZER_NONE,
        'php' => \Redis::SERIALIZER_PHP,
        'igbinary' => 2  // IGBINARY constant is not available some times.
    );

    /**
     * Constructor
     * 
     * @param object $redis  \Redis
     * @param object $params Service parameters
     */
    public function __construct(\Redis $redis, array $params)
    {
        $this->redis  = $redis;
        $this->params = $params;

        if (! $this->connect()) {
            throw new RuntimeException(
                sprintf(
                    'Cache handler %s connection failed.',
                    get_class()
                )
            );
        }
    }

    /**
     * Connect to Redis
     * 
     * @return boolean
     */
    public function connect()
    {
        if ($this->redis->isConnected()) {
            return true;
        }
        return false;
    }

    /**
     * If method does not exist in this class call it from $this->redis
     * 
     * @param string $method    methodname
     * @param array  $arguments method arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->redis, $method), $arguments);
    }

    /**
     * Get current serializer name
     * 
     * @return string serializer name
     */
    public function getSerializer()
    {
        $number = $this->getOption('OPT_SERIALIZER');
        $serializers = array_flip($this->serializers);
        return $serializers[$number];
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
        if ($serializer == 'igbinary') {
            $class = new ReflectionClass("Redis");
            if (! $class->hasConstant("SERIALIZER_IGBINARY")) {
                throw new RuntimeException("Igbinary is not enabled on your redis installation.");
            }
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
        }
        $this->redis->setOption(\Redis::OPT_SERIALIZER, $this->serializers[$serializer]); 
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
        $obj = new ReflectionClass('Redis');
        
        if (is_string($option)) {
            $option = $obj->getconstant($option);
        }
        return $this->redis->getOption($option);
    }

    /**
     * Set option
     * 
     * @param mixed $option integer option or string constant name
     * @param mixed $value  mixed   value or constant name
     *
     * @return void
     */
    public function setOption($option = 'OPT_SERIALIZER', $value = 'SERIALIZER_PHP')
    {
        $obj = new ReflectionClass('Redis');

        if (is_string($option)) {
            $option = $obj->getconstant($option);
        }
        if ($obj->getconstant($value)) {
            $value = $obj->getconstant($value);
        }
        $this->redis->setOption($option, $value); 
    }

    /**
     * Get cache data.
     * 
     * @param string $key cache key.
     * 
     * @return mix
     */
    public function getItem($key)
    {
        return $this->redis->get($key);
    }

    /**
     * Get cache data.
     * 
     * @param string $key cache key.
     * 
     * @return mix
     */
    public function getItems(array $key)
    {
        return $this->redis->getMultiple($key);
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
        return $this->redis->exists($key);
    }

    /**
     * Returns the keys that match a certain pattern.
     * 
     * @param string $pattern pattern symbol
     * 
     * @return array the keys that match a certain pattern.
     */
    public function getAllKeys($pattern = '*')
    {
        return $this->redis->keys($pattern);
    }

    /**
     * Get All Data
     * 
     * @return array return all the key and data
     */
    public function getAllData()
    {
        $keys = $this->redis->keys('*');
        if (sizeof($keys) == 0) {
            return $keys;
        }
        foreach ($keys as $v) {
            $getData = $this->redis->get($v);
            if (empty($getData)) {
                $getData = $this->sGetMembers($v);
            }
            $data[$v] = $getData;
        }
        return $data;
    }

    /**
     * Adds a value to the hash stored at key. If this value is already in the hash, FALSE is returned.
     * 
     * @param string $key     cache key.
     * @param string $hashKey hash key.
     * @param string $data    cache data.
     * @param int    $ttl     expiration time
     * 
     * @return LONG 1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
     */
    public function hSet($key, $hashKey, $data, $ttl = 0)
    {
        $hSet = $this->redis->hSet($key, $hashKey, $data);
        if ((int)$ttl > 0) {
            $this->redis->setTimeout($key, (int)$ttl);
        }
        return $hSet;
    }

    /**
     * Fills in a whole hash. Non-string values are converted to string, using the standard (string) cast. NULL values are stored as empty strings.
     * 
     * @param string $key     cache key.
     * @param array  $members key - value array.
     * @param int    $ttl     expiration
     * 
     * @return bool
     */
    public function hMSet($key, $members, $ttl = 0)
    {
        $hMSet = $this->redis->hMSet($key, $members);
        if ((int)$ttl > 0) {
            $this->redis->setTimeout($key, (int)$ttl);
        }
        return $hMSet;
    }

    /**
     * Set cache data.
     * 
     * @param mix $key  cache key or data.
     * @param mix $data cache data or default expiration time.
     * @param int $ttl  expiration time
     * 
     * @return boolean
     */
    public function setItem($key, $data, $ttl = 60) // If empty $ttl default timeout unlimited
    {
        return $this->redis->set($key, $data, $ttl);
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
     * Remove specified keys.
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function removeItem($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * Remove specified keys.
     * 
     * @param array $keys key - value
     * 
     * @return void
     */
    public function removeItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->removeItem($key);
        }
        return;
    }

    /**
     * Replace key value
     * 
     * @param string $key  redis key
     * @param mix    $data cache data
     * @param int    $ttl  sec
     * 
     * @return boolean
     */
    public function replaceItem($key, $data, $ttl = 60)
    {
        return $this->redis->set($key, $data, $ttl);
    }

    /**
     * Replace keys
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
     * Get software information installed on your server.
     * 
     * @return object
     */
    public function getInfo()
    {
        return $this->redis->info();
    }

    /**
     * Set Array
     * 
     * @param array $data cache data.
     * @param int   $ttl  expiration time.
     * 
     * @return boolean
     */
    protected function setArray(array $data, $ttl)
    {
        foreach ($data as $k => $v) {
            $this->redis->set($k, $v, $ttl);
        }
        return true;
    }

    /**
     * Flush all items in 1 seconds (default)
     * 
     * @return boolean
     */
    public function flushAll()
    {
        return $this->redis->flushDB();
    }

}