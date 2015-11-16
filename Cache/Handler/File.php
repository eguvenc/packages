<?php

namespace Obullo\Cache\Handler;

use RuntimeException;
use Obullo\Cache\CacheInterface;
use Obullo\Config\ConfigInterface;

/**
 * File Caching Class
 *
 * @category  Cache
 * @package   File
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/cache
 */
class File implements CacheInterface
{
    const SERIALIZER_NONE = 'none';

    /**
     * Uploaded file path
     * 
     * @var string
     */
    public $filePath;

    /**
     * Constructor
     * 
     * @param object $config \Obullo\Config\ConfigInterface
     */
    public function __construct(ConfigInterface $config)
    {
        $this->filePath = $config->load('cache/file')['path'];
        $filePath = ltrim($this->filePath, '/');

        if (strpos($filePath, 'resources') === 0) {
            $this->filePath = ROOT. $filePath . '/';
        }
        if (! is_writable($this->filePath)) {
            throw new RuntimeException(
                sprintf(
                    'Filepath %s is not writable.',
                    $this->filePath
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
        if (! file_exists($this->filePath . $key)) {
            return false;
        }
        $data = file_get_contents($this->filePath . $key);
        $data = unserialize($data);

        if (time() > $data['time'] + $data['ttl']) {
            unlink($this->filePath . $key);
            return false;
        }
        return $data['data'];
    }

    /**
     * Verify if the specified key exists.
     * 
     * @param string $key storage key
     * 
     * @return boolean true or false
     */
    public function exists($key)
    {
        if ($this->get($key) == false) {
            return false;
        }
        return true;
    }

    /**
     * Replace cache data.
     * 
     * @param string  $key  key
     * @param string  $data string data
     * @param integer $ttl  expiration
     * 
     * @return boolean
     */
    public function replace($key, $data = 60, $ttl = 60)
    {
        if (! is_array($key)) {
            $this->delete($key);
            $contents = array(
                'time' => time(),
                'ttl'  => $ttl,
                'data' => $data
            );
            $fileName = $this->filePath . $key;
            if ($this->writeData($fileName, $contents)) {
                return true;
            }
            return false;
        }
        return $this->setArray($key, $data);
    }

    /**
     * Set Array
     * 
     * @param array $data data
     * @param int   $ttl  expiration
     *
     * @return void
     */
    public function setArray($data, $ttl)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $contents = array(
                    'time' => time(),
                    'ttl'  => $ttl,
                    'data' => $v
                );
                $fileName = $this->filePath . $k;
                $write    = $this->writeData($fileName, $contents);
            }
            if (! $write) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Write data
     *
     * @param string $fileName file name
     * @param array  $contents contents
     * 
     * @return boolean true or false
     */
    public function writeData($fileName, $contents)
    {
        if (! $fp = fopen($fileName, 'wb')) {
            return false;
        }
        $serializeData = serialize($contents);
        flock($fp, LOCK_EX);
        fwrite($fp, $serializeData);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    /**
     * Save data
     * 
     * @param string $key  cache key.
     * @param array  $data cache data.
     * @param int    $ttl  expiration time.
     * 
     * @return boolean
     */
    public function set($key, $data = 60, $ttl = 60)
    {
        if (! is_array($key)) {
            $contents = array(
                'time' => time(),
                'ttl'  => $ttl,
                'data' => $data
            );
            $fileName = $this->filePath . $key;
            if ($this->writeData($fileName, $contents)) {
                return true;
            }
            return false;
        }
        return $this->setArray($key, $data);
    }

    /**
     * Delete
     * 
     * @param string $key cache key.
     * 
     * @return boolean
     */
    public function delete($key)
    {
        if (file_exists($this->filePath . $key)) {
            return unlink($this->filePath . $key);
        }
        return false;
    }

    /**
     * Get all keys
     * 
     * @return array
     */
    public function getAllKeys()
    {
        $dh  = opendir($this->filePath);
        while (false !== ($fileName = readdir($dh))) {
            if (substr($fileName, 0, 1) !== '.') {
                $files[] = $fileName;
            }
        }
        return $files;
    }

    /**
     * Get all data
     * 
     * @return array
     */
    public function getAllData()
    {
        $dh  = opendir($this->filePath);
        while (false !== ($fileName = readdir($dh))) {
            if (substr($fileName, 0, 1) !== '.') {
                $temp = file_get_contents($this->filePath . $fileName);
                $temp = unserialize($temp);
                if (time() > $temp['time'] + $temp['ttl']) {
                    unlink($this->filePath . $fileName);
                    return false;
                }
                $data[$fileName] = $temp['data'];
            }
        }
        return (empty($data)) ? null : $data;
    }

    /**
     * Clean all data
     * 
     * @return boolean
     */
    public function flushAll()
    {
        $dh  = opendir($this->filePath);
        while (false !== ($fileName = readdir($dh))) {
            if (substr($fileName, 0, 1) !== '.') {
                unlink($this->filePath . $fileName);
            }
        }
    }

    /**
     * Cache Info
     * 
     * @return array
     */
    public function info()
    {
        return scandir($this->filePath);
    }

    /**
     * Get Meta Data
     * 
     * @param string $key cache key.
     * 
     * @return array otherwise boolean
     */
    public function getMetaData($key)
    {
        if (! file_exists($this->filePath . $key)) {
            return false;
        }
        $data = file_get_contents($this->filePath . $key);
        $data = unserialize($data);

        if (is_array($data)) {
            $mtime = filemtime($this->filePath . $key);
            if (! isset($data['ttl'])) {
                return false;
            }
            return array(
                'expire' => $mtime + $data['ttl'],
                'mtime' => $mtime
            );
        }
        return false;
    }

    /**
     * Connect to file.
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