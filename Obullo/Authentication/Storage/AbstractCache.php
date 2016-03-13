<?php

namespace Obullo\Authentication\Storage;

use Obullo\Session\SessionInterface as Session;
use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Common cache storage
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractCache extends AbstractStorage implements StorageInterface
{
    protected $params;          // Config parameters
    protected $cache;           // Cache class
    protected $cacheKey;        // Cache key
    protected $request;         // Request class
    protected $session;         // Session class

    /**
     * Constructor
     * 
     * @param object $container $container
     * @param object $request   server request
     * @param object $session   session
     * @param array  $params    parameters
     */
    public function __construct(Container $container, Request $request, Session $session, array $params) 
    {
        $this->params = $params;
        $this->request = $request;
        $this->cacheKey = (string)$params['cache']['key'];
        $this->session = $session;

        $this->connect($container, $params);
    }

    /**
     * Returns true if temporary credentials does "not" exists
     *
     * @param string $block __temporary or __permanent | full key
     * 
     * @return bool
     */
    public function isEmpty($block = '__permanent')
    {
        $exists = $this->cache->hasItem($this->getBlock($block));
        return ($exists) ? false : true;
    }

    /**
     * Match the user credentials.
     * 
     * @return object|false
     */
    public function query()
    {
        if (! $this->isEmpty('__permanent')) {  // If user has cached auth return to data otherwise false

            $data = $this->getCredentials($this->getMemoryBlockKey('__permanent'));

            if (count($data) == 0 || ! isset($data['__isAuthenticated']) ) {
                return false;
            }
            return $data;
        }
        return false;
    }
    
    /**
     * Update credentials
     * 
     * @param array  $credentials user identity old data
     * @param mixed  $pushData    push to identity data
     * @param string $block       storage persistence type permanent / temporary
     * @param string $ttl         storage lifetime
     * 
     * @return boolean
     */
    public function setCredentials(array $credentials, $pushData = null, $block = '__temporary', $ttl = null)
    {
        if ($this->getIdentifier() == '__empty') {
            return false;
        }
        $this->data[$block] = array($this->getLoginId() => $credentials);
        if (! empty($pushData) && is_array($pushData)) {
            $this->data[$block] = array($this->getLoginId() => array_merge($credentials, $pushData));
        }
        $allData = $this->cache->getItem($this->getMemoryBlockKey($block));  // Get all data
        $lifetime = ($ttl == null) ? $this->getMemoryBlockLifetime($block) : (int)$ttl;

        if ($allData == false) {
            $allData = array();
        }
        return $this->cache->setItem($this->getMemoryBlockKey($block), array_merge($allData, $this->data[$block]), $lifetime);
    }

    /**
     * Get Temporary Credentials Data
     *
     * @param string $block name
     * 
     * @return void
     */
    public function getCredentials($block = '__permanent')
    {
        if ($this->getIdentifier() == '__empty') {
            return false;
        }
        $data = $this->cache->getItem($this->getBlock($block));
        if (isset($data[$this->getLoginId()])) {
            return $data[$this->getLoginId()];
        }
        return false;
    }

    /**
     * Deletes memory block
     *
     * @param string $block name or key
     * 
     * @return void
     */
    public function deleteCredentials($block = '__permanent')
    {
        $loginID = $this->getLoginId();
        $credentials = $this->cache->getItem($this->getBlock($block));  // Don't do container cache

        if (! isset($credentials[$loginID])) {  // already removed
            return;
        }
        unset($credentials[$loginID]);
        $this->cache->setItem(
            $this->getMemoryBlockKey($block),
            $credentials,
            $this->getMemoryBlockLifetime($block)
        );
        $credentials = $this->cache->getItem($this->getBlock($block)); // Destroy auth block if its empty
        if (empty($credentials)) {
            $this->cache->removeItem($this->getBlock($block));
        }
    }

    /**
     * Update identity item value
     * 
     * @param string $key   string
     * @param value  $val   value
     * @param string $block block key
     *
     * @return boolean|integer
     */
    public function update($key, $val, $block = '__permanent')
    {
        $data = $this->getCredentials($block);
        $data[$key] = $val;
        $this->setCredentials($data, null, $block);
    }

    /**
     * Unset identity item
     * 
     * @param string $key   string
     * @param string $block block key
     * 
     * @return boolean|integer
     */
    public function remove($key, $block = '__permanent')
    {
        $data = $this->getCredentials($block);
        unset($data[$key]);
        $this->setCredentials($data, null, $block);
    }

    /**
     * Get all keys
     *
     * @param string $block __temporary or __permanent
     * 
     * @return array keys if succes otherwise false
     */
    public function getAllKeys($block = '__permanent')
    {
        return $this->cache->getItem($this->getBlock($block));
    }

    /**
     * Returns to full identity block name
     *
     * @param string $block name
     * 
     * @return string
     */
    public function getMemoryBlockKey($block = '__temporary')
    {
        return $this->getCacheKey(). ':' .$block. ':' .$this->getUserId();  // Create unique key
    }
    
    /**
     * Returns to storage prefix key of identity data
     *
     * @param string $block memory block
     * 
     * @return string
     */
    public function getUserKey($block = '__temporary')
    {
        return $this->getCacheKey(). ':' .$block. ':'.$this->getUserId();
    }

    /**
     * Returns to database sessions
     * 
     * @return array
     */
    public function getUserSessions()
    {
        $sessions = array();
        $dbSessions = $this->cache->getItem($this->getMemoryBlockKey('__permanent'));
        if ($dbSessions == false) {
            return $sessions;
        }
        foreach ($dbSessions as $loginID => $val) {
            if (isset($val['__isAuthenticated'])) {
                $sessions[$loginID]['__isAuthenticated'] = $val['__isAuthenticated'];
                $sessions[$loginID]['__time'] = $val['__time'];
                $sessions[$loginID]['id']  = $this->getUserId();
                $sessions[$loginID]['key'] = $this->getMemoryBlockKey('__permanent');   
            }
        }
        return $sessions;
    }

    /**
     * Kill session using by login id
     * 
     * @param integer $loginID login id e.g. 87060e89 ( user@example.com:87060e89 )
     * 
     * @return void
     */
    public function killSession($loginID)
    {
        $data = $this->cache->getItem($this->getMemoryBlockKey('__permanent'));
        unset($data[$loginID]);
        $this->cache->setItem(
            $this->getMemoryBlockKey('__permanent'),
            $data,
            $this->getMemoryBlockLifetime('__permanent')
        );
    }

}