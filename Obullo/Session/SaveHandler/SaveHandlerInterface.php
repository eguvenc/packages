<?php

namespace Obullo\Session\SaveHandler;

use Obullo\Container\ServiceProviderInterface;

/**
 * Save Handler Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface SaveHandlerInterface
{
    /**
     * Constructor
     *
     * @param object $provider \Obullo\Service\ServiceProviderInterface
     * @param array  $params   service parameters
     */
    public function __construct(ServiceProviderInterface $provider, array $params);

    /**
    * Php5 session handler interface open function
    * 
    * @param string $savePath    save path 
    * @param string $sessionName session name
    * 
    * @return bool
    */
    public function open($savePath, $sessionName);

    /**
     * Close the connection. Called by PHP when the script ends.
     * 
     * @return void
     */
    public function close();

    /**
     * Read data from the session.
     * 
     * @param string $id session id
     * 
     * @return mixed
     */
    public function read($id);

    /**
     * Write data to the session.
     * 
     * @param string $id   current session id
     * @param mixed  $data mixed data
     * 
     * @return bool
     */
    public function write($id, $data);

    /**
     * Delete data from the session.
     * 
     * @param string $id current session id
     * 
     * @return bool
     */
    public function destroy($id);

    /**
     * Run garbage collection
     * 
     * @param integer $maxLifetime expration time
     * 
     * @return bool
     */
    public function gc($maxLifetime);

    /**
     * Set expiration of valid session
     * 
     * @param int $ttl lifetime
     * 
     * @return void
     */
    public function setLifetime($ttl);

    /**
     * Get expiration of valid session
     * 
     * @return int
     */
    public function getLifetime();
}