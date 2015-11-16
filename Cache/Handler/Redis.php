<?php

namespace Obullo\Cache\Handler;

use ReflectionClass;
use RuntimeException;
use Obullo\Cache\CacheInterface;
use Obullo\Config\ConfigInterface;

/**
 * Redis Caching Class
 *
 * @category  Cache
 * @package   Redis
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/cache
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
     * Available serializers
     * 
     * @var array
     */
    public $serializers = array(
        0 => 'none',
        1 => 'php',
        2 => 'igbinary'
    );

    /**
     * Constructor
     * 
     * @param object $config \Obullo\Config\ConfigInterface
     * @param object $redis  \Redis
     */
    public function __construct(ConfigInterface $config, \Redis $redis)
    {
        $this->redis = $redis;
        $this->config = $config->load('cache/redis');

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
        $this->openNodeConnections();

        if ($this->redis->isConnected()) {
            return true;
        }
        return false;
    }

    /**
     * Connect to redis nodes
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
            $this->redis->slaveof($servers['host'], $servers['port']);
        }
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
        $this->redis->setOption(Redis::OPT_SERIALIZER, $this->serializers[$serializer]);
    }

    /**
     * Get client option.
     *
     * @param string $option option constant
     * 
     * @return string value
     */
    public function getOption($option = 'OPT_SERIALIZER')
    {
        $obj = new ReflectionClass('Redis');
        $constant = $obj->getconstant($option);
        return $this->redis->getOption($constant);
    }

    /**
     * Set option
     * 
     * @param string $option constant name
     * @param string $value  constant value name
     *
     * @return void
     */
    public function setOption($option = 'OPT_SERIALIZER', $value = 'SERIALIZER_NONE')
    {
        $obj    = new ReflectionClass('Redis');
        $option = $obj->getconstant($option);
        $value  = $obj->getconstant($value);

        $this->redis->setOption($option, $value); 
    }

    /**
     * Get cache data.
     * 
     * @param string $key cache key.
     * 
     * @return mix
     */
    public function get($key)
    {
        return $this->redis->get($key);
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
        return $this->redis->exists($key);
    }

    /**
     * Renames a key.
     * 
     * @param string $key    cache key.
     * @param string $newKey cache key.
     * 
     * @return boolean true or false
     */
    public function renameKey($key, $newKey)
    {
        return $this->redis->rename($key, $newKey);
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
     * Sort the elements in a list, set or sorted set.
     * 
     * @param string $key  cache key.
     * @param array  $sort optional
     * 
     * @return array the keys that match a certain pattern.
     */
    public function sort($key, $sort = array())
    {
        if (count($sort) > 0) {
            return $this->redis->sort($key, $sort);
        }
        return $this->redis->sort($key);
    }

    /**
     * Adds a value to the set value stored at key. If this value is already in the set, FALSE is returned.
     * 
     * @param string $key  cache key.
     * @param string $data cache data.
     * 
     * @return long the number of elements added to the set.
     */
    public function sAdd($key, $data)
    {
        if (is_array($data)) {
            $data = "'" . implode("','", $data) . "'";
        }
        return $this->redis->sAdd($key, $data);
    }

    /**
     * Returns the cardinality of the set identified by key.
     * 
     * @param string $key cache key.
     * 
     * @return long the cardinality of the set identified by key, 0 if the set doesn't exist.
     */
    public function sSize($key)
    {
        return $this->redis->sCard($key);
    }

    /**
     * Returns the members of a set resulting from the intersection of all the sets held at the specified keys.
     * 
     * @param array $keys cache keys.
     * 
     * @return array contain the result of the intersection between those keys.
     * If the intersection beteen the different sets is empty,
     * the return value will be empty array.
     */
    public function sInter($keys = array())
    {
        if (count($keys) > 0 AND is_array($keys)) {
            return $this->redis->sInter("'" . implode("','", $keys) . "'");
        }
        return false;
    }

    /**
     * Returns the contents of a set.
     * 
     * @param string $key cache key.
     * 
     * @return array of elements, the contents of the set.
     */
    public function sGetMembers($key)
    {
        return $this->redis->sMembers($key);
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
     * Set Array
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
                $this->redis->set($k, $v, $ttl);
            }
            return $this;
        }
        return false;
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
    public function set($key, $data = 60, $ttl = 60) // If empty $ttl default timeout unlimited
    {
        if (! is_array($key)) {
            return $this->redis->set($key, $data, $ttl);
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
        return $this->redis->delete($key);
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
    public function replace($key, $data = 60, $ttl = 60)
    {
        if (! is_array($key)) {
            return $this->redis->set($key, $data, $ttl);
        }
        return $this->setArray($key, $data);
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