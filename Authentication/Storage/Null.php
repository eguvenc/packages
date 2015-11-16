<?php

namespace Obullo\Authentication\Storage;

use Obullo\Container\ServiceProviderInterface;
use Obullo\Session\SessionInterface as Session;

/**
 * Null Storage
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Null extends AbstractNull implements StorageInterface
{
    protected $params;          // Config parameters
    protected $cacheKey;        // Cache key
    protected $session;         // Session class

    /**
     * Constructor
     * 
     * @param object $session  session
     * @param object $provider provider
     * @param array  $params   parameters
     */
    public function __construct(Session $session, ServiceProviderInterface $provider, array $params)
    {
        $provider = $params = null;
        $this->cacheKey = (string)$params['cache.key'];
        $this->session = $session;
    }

    /**
     * Connect to cache provider
     * 
     * @return boolean
     */
    public function connect()
    {
        return true;
    }

    /**
     * Returns true if temporary credentials does "not" exists
     *
     * @param string $block __temporary or __permanent | full key
     * 
     * @return bool
     */
    public function isEmpty($block = '')
    {
        $block = null;
        return true;
    }

    /**
     * Match the user credentials.
     * 
     * @return object|false
     */
    public function query()
    {
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
        $loginID = $this->getLoginId();

        $this->data[$block] = array($loginID => $credentials);
        if (! empty($pushData) && is_array($pushData)) {
            $this->data[$block] = array($loginID => array_merge($credentials, $pushData));
        }
        $ttl = null;
        $allData = $this->session->get($this->getMemoryBlockKey($block));  // Get all data

        if ($allData == false) {
            $allData = array();
        }
        return $this->session->set($this->getMemoryBlockKey($block), array_merge($allData, $this->data[$block]));
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
        $loginID = $this->getLoginId();
        $data = $this->session->get($this->getBlock($block));
        if (isset($data[$loginID])) {
            return $data[$loginID];
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
    public function deleteCredentials($block = '__temporary')
    {
        $loginID = $this->getLoginId();
        $credentials = $this->session->get($this->getBlock($block));  // Don't do container cache

        if (! isset($credentials[$loginID])) {  // already removed
            return;
        }
        unset($credentials[$loginID]);
        $this->session->set($this->getMemoryBlockKey($block), $credentials);

        $credentials = $this->session->get($this->getBlock($block)); // Destroy auth block if its empty

        if (empty($credentials)) {
            $this->session->remove($this->getBlock($block));
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
     * Check whether to identify exists
     *
     * @param string $block __temporary or __permanent
     * 
     * @return array keys if succes otherwise false
     */
    public function getAllKeys($block = '')
    {
        $block = null;
        return array();
    }

    /**
     * Returns to database sessions
     * 
     * @return array
     */
    public function getUserSessions()
    {
        return array();
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
        $loginID = null;
        return false;
    }
}