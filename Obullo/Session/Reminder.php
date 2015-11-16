<?php

namespace Obullo\Session;

use Obullo\Config\ConfigInterface;
use Obullo\Container\ContainerInterface;

/**
 * Session Reminder Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
     * @param ContainerInterface $c       container
     * @param SessionInterface   $session session
     */
    public function __construct(Container $c, SessionInterface $session)
    {
        $this->session = $session;
        $this->params = $c['session.params'];
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
        session_set_cookie_params(
            $lifetime,
            $this->params['cookie']['path'],
            $this->params['cookie']['domain'],
            $this->params['cookie']['secure'],
            $this->params['cookie']['httpOnly']
        );
        if ($this->session->exists()) {
            $this->session->regenerateId($deleteOldSession, $lifetime); // There is a running session so we will regenerate id to send a new cookie.
        }
    }
}