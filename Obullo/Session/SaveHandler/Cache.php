<?php

namespace Obullo\Session\SaveHandler;

use Obullo\Container\ServiceProvider\ServiceProviderInterface as ServiceProvider;

/**
 * Cache Save Handler
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Cache implements SaveHandlerInterface
{
    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Storage
     * 
     * @var object
     */
    protected $storage;

    /**
     * Redis key name
     * 
     * @var string
     */
    public $key = 'sessions:';

    /**
     * Expiration time of current session
     * 
     * @var integer
     */
    public $lifetime = 7200; // two hours
 
    /**
     * Constructor
     *
     * @param object $provider provider
     * @param array  $params   service parameters
     */
    public function __construct(ServiceProvider $provider, array $params)
    {
        $this->params = $params;
        $this->provider = $provider;
        $this->key = $this->params['storage']['key'];
        $this->lifetime = $this->params['storage']['lifetime'];
    }

    /**
    * Php5 session handler : open storage connection
    * 
    * @param string $savePath    save path 
    * @param string $sessionName session name
    * 
    * @return bool
    */
    public function open($savePath, $sessionName)
    {
        $savePath = $sessionName = null;
        $this->storage = $this->provider->shared(
            [
                'connection' => $this->params['provider']['connection']
            ]
        );
        return is_object($this->storage) ? true : false;
    }
 
    /**
     * Close the connection. Called by PHP when the script ends.
     * 
     * @return void
     */
    public function close()
    {
        return;
    }
 
    /**
     * Read data from the session.
     * 
     * @param string $id session id
     * 
     * @return mixed
     */
    public function read($id)
    {
        $result = $this->storage->get($this->key.$id);
        return $result ?: null;
    }
 
    /**
     * Write data to the session.
     * 
     * @param string $id   current session id
     * @param mixed  $data mixed data
     * 
     * @return bool
     */
    public function write($id, $data)
    {
        if (empty($data)) { // If we have no session data don't write it.
            return false;
        }
        $result = $this->storage->set($this->key.$id, $data, $this->getLifetime());
        return $result ? true : false;
    }
 
    /**
     * Delete data from the session.
     * 
     * @param string $id current session id
     * 
     * @return bool
     */
    public function destroy($id)
    {
        $result = $this->storage->delete($this->key.$id);
        return $result ? true : false;
    }

    /**
     * Run garbage collection
     * 
     * @param integer $maxLifetime expration time
     * 
     * @return bool
     */
    public function gc($maxLifetime)
    {
        $maxLifetime = null;
        return true;
    }

    /**
     * Set expiration of valid session
     * 
     * @param int $ttl lifetime
     * 
     * @return void
     */
    public function setLifetime($ttl)
    {
        $this->lifetime = (int)$ttl;
    }

    /**
     * Get expiration of valid session
     * 
     * @return int
     */ 
    public function getLifetime()
    {
        return $this->lifetime;
    }
}