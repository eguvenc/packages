<?php

namespace Obullo\Authentication\Storage;

/**
 * Null Adapter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractNull
{
    /**
     * Sets identifier value to session
     *
     * @param string $identifier user id
     * 
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->session->set($this->getCacheKey().'/Identifier', $identifier);
    }

    /**
     * Returns to user identifier
     * 
     * @return mixed string|id
     */
    public function getIdentifier()
    {
        $id = $this->session->get($this->getCacheKey().'/Identifier');

        return empty($id) ? '__empty' : $id.':'.$this->getLoginId();
    }

    /**
     * Unset identifier from session
     * 
     * @return void
     */
    public function unsetIdentifier()
    {   
        $this->session->remove($this->getCacheKey().'/Identifier');
    }

    /**
     * Check user has identifier
     * 
     * @return bool
     */
    public function hasIdentifier()
    {
        return ($this->getIdentifier() == '__empty') ? false : true;
    }

    /**
     * Register credentials to temporary storage
     * 
     * @param array $credentials user identities
     * 
     * @return void
     */
    public function createTemporary(array $credentials)
    {
        return $credentials = null;
    }

    /**
     * Register credentials to permanent storage
     * 
     * @param array $credentials user identities
     * 
     * @return void
     */
    public function createPermanent(array $credentials)
    {
        $credentials['__isAuthenticated'] = 1;
        $credentials['__isTemporary'] = 0;
        $this->setCredentials($credentials, null, '__permanent', $this->getMemoryBlockLifetime('__permanent'));
    }

    /**
     * Makes temporary credentials as permanent and authenticate the user.
     * 
     * @return mixed false|array
     */
    public function makePermanent()
    {
        return false;
    }

    /**
     * Makes permanent credentials as temporary and unauthenticate the user.
     * 
     * @return mixed false|array
     */
    public function makeTemporary()
    {
        return false;
    }

    /**
     * Get id of identifier without random Id value
     * 
     * @return string
     */
    public function getUserId()
    {
        $identifier = $this->session->get($this->getCacheKey().'/Identifier');
        if (empty($identifier)) {
            return '__empty';
        }
        return $identifier; // user@example.com
    }

    /**
     * Get random id
     * 
     * @return string
     */
    public function getLoginId()
    {
        $id = $this->session->get($this->getCacheKey().'/LoginId');
        if ($id == false) {
            $id = $this->setLoginId();
            return $id;
        }
        return $id;
    }

    /**
     * Create login id
     * 
     * @return string
     */
    public function setLoginId()
    {
        $server = $this->request->getServerParams();

        $agentStr  = isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : null;
        $userAgent = substr($agentStr, 0, 50);  // First 50 characters of the user agent
        $id = hash('adler32', trim($userAgent));

        $this->session->set($this->getCacheKey().'/LoginId', $id);
        return $id;
    }

    /**
     * Gey cache key
     * 
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

        /**
     * Get valid memory segment
     * 
     * @param string $block name
     * 
     * @return string
     */
    protected function getBlock($block)
    {
        return ($block == '__temporary' || $block == '__permanent') ? $this->getMemoryBlockKey($block) : $block;
    }

    /**
     * Returns to storage full key of identity data
     *
     * @param string $block name
     * 
     * @return string
     */
    public function getMemoryBlockKey($block = '__temporary')
    {
        return $this->cacheKey. ':' .$block. ':' .$this->getIdentifier();  // Create unique key
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
        return $this->cacheKey. ':' .$block. ':'.$this->getUserId();
    }

    /**
     * Returns to memory block lifetime
     * 
     * @param array $block __temporary or __permanent
     * 
     * @return integer
     */
    protected function getMemoryBlockLifetime($block = '__temporary')
    {
        if ($block == '__temporary') {
            return (int)$this->params['cache']['block']['temporary']['lifetime'];
        }
        return (int)$this->params['cache']['block']['permanent']['lifetime'];
    }

}