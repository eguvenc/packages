<?php

namespace Obullo\Session;

use Obullo\Container\ContainerInterface as Container;

/**
 * Session Reminder Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Reminder
{
    /**
     * Service parameters
     * 
     * @var array
     */
    public $params;

    /**
     * Session Class
     * 
     * @var object
     */
    public $session;

    /**
     * Constructor 
     * 
     * @param ContainerInterface $container container
     * @param SessionInterface   $session   session
     */
    public function __construct(Container $container, Session $session)
    {
        $this->session = $session;
        $this->params = $container['session.params'];
    }

    /**
     * Set the TTL (in seconds) for the session cookie expiry
     *
     * Can safely be called in the middle of a session.
     *
     * @param null $ttl              expiration   null or integer
     * @param bool $deleteOldSession whether to delete old session data after renenerate
     * 
     * @return void
     */
    public function rememberMe($ttl = null, $deleteOldSession = true)
    {
        $this->setSessionCookieLifetime($ttl, $deleteOldSession);
    }

    /**
     * Set a 0s TTL for the session cookie
     *
     * Can safely be called in the middle of a session.
     *
     * @return SessionManager
     */
    public function forgetMe()
    {
        $this->setSessionCookieLifetime(0);
    }

    /**
     * Set the session cookie lifetime
     *
     * If a session already exists, destroys it (without sending an expiration
     * cookie), regenerates the session ID, and restarts the session.
     *
     * @param int  $lifetime         expiration
     * @param bool $deleteOldSession whether to delete old session data after renenerate
     * 
     * @return void
     */
    protected function setSessionCookieLifetime($lifetime, $deleteOldSession = true)
    { 
        if ($lifetime == null) {
            $lifetime = $this->params['storage']['lifetime'];
        }
        session_set_cookie_params(
            $lifetime,
            $this->params['cookie']['path'],
            $this->params['cookie']['domain'],
            $this->params['cookie']['secure'],
            $this->params['cookie']['httpOnly']
        );
        if ($this->session->exists()) {
            
            // If session exists
            // we need regenerate id to send a new cookie.

            $this->session->regenerateId($deleteOldSession, $lifetime);
        }
    }
}