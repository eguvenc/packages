<?php

namespace Obullo\Session;

use Obullo\Session\SessionInterface as Session;

/**
 * Session Reminder Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Reminder
{
    /**
     * Session Class
     * 
     * @var object
     */
    public $session;

    /**
     * Constructor 
     * 
     * @param SessionInterface $session session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
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
        $params = $this->session->getParams();

        if ($lifetime == null) {
            $lifetime = $params['storage']['lifetime'];
        }
        session_set_cookie_params(
            $lifetime,
            $params['cookie']['path'],
            $params['cookie']['domain'],
            $params['cookie']['secure'],
            $params['cookie']['httpOnly']
        );
        if ($this->session->exists()) {
            
            // If session exists
            // we need regenerate id & set cookie.

            $this->session->regenerateId($deleteOldSession, $lifetime);
        }
    }
}