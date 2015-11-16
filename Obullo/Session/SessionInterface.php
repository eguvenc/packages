<?php

namespace Obullo\Session;

/**
 * Session Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface SessionInterface
{
    /**
     * Set session name
     * 
     * @param string $name session name
     *
     * @return void
     */
    public function setName($name = null);

    /**
     * Get name of the session
     * 
     * @return string
     */
    public function getName();

    /**
     * Session start
     * 
     * @return void
     */
    public function start();

    /**
     * Regenerate id
     *
     * @param bool $deleteOldSession whether to delete previous session data
     * @param int  $lifetime         max lifetime of session
     * 
     * @return string new session id
     */
    public function regenerateId($deleteOldSession = true, $lifetime = null);

    /**
     * Does a session exist and is it currently active ?
     *
     * @return bool
     */
    public function exists();

    /**
     * Destroy the current session
     *
     * @return void
     */
    public function destroy();

    /**
     * Fetch a specific item from the session array
     *
     * @param string $item   session key
     * @param string $prefix session key prefix
     * 
     * @return string
     */
    public function get($item, $prefix = '');

    /**
     * Add or change data in the $_SESSION
     * 
     * @param mixed  $new    key or array
     * @param string $newval value
     * @param string $prefix prefix
     * 
     * @return void
     */
    public function set($new = array(), $newval = '', $prefix = '');

    /**
     * Delete a session variable from the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $prefix sesison key prefix
     * 
     * @return void
     */
    public function remove($new = array(), $prefix = '');

    /**
     * Returns to all session data
     * 
     * @return data
     */
    public function getAll();

}