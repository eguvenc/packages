<?php

namespace Obullo\Session;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Container\ServiceProviderInterface as ServiceProvider;

use Psr\Http\Message\RequestInterface as Request;

/**
 * Session Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Session implements SessionInterface
{
    /**
     * Session name
     * 
     * @var string
     */
    protected $name;

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Session handler
     * 
     * @var object
     */
    protected $saveHandler;

    /**
     * Constructor
     * 
     * @param object $provider \Obullo\Service\ServiceProviderInterface
     * @param object $request  \Psr\Http\Message\RequestInterface
     * @param object $logger   \Obullo\Log\LoggerInterface
     * @param array  $params   service parameters
     */
    public function __construct(ServiceProvider $provider, Request $request, Logger $logger, array $params) 
    {
        $this->params = $params;
        $this->provider = $provider;

        $this->server = $request->getServerParams();
        $this->cookie = $request->getCookieParams();

        ini_set('session.cookie_domain', $this->params['cookie']['domain']);

        $this->logger = $logger;
        $this->logger->debug('Session Class Initialized');

        register_shutdown_function(array($this, 'close'));
    }

    /**
     * Set service parameters using class methods
     * 
     * @param array $params service params
     * 
     * @return void
     */
    public function setParameters(array $params)
    {
        foreach ($params as $method => $arg) {
            $this->{$method}($arg);
        }
    }

    /**
     * Register session save handler
     *
     * If save handler not provided we call it from config file
     * 
     * @param string $handler save handler object
     * 
     * @return void
     */
    public function registerSaveHandler($handler)
    {
        $this->saveHandler = new $handler($this->provider, $this->params);
        session_set_save_handler(
            array($this->saveHandler, 'open'),
            array($this->saveHandler, 'close'),
            array($this->saveHandler, 'read'),
            array($this->saveHandler, 'write'),
            array($this->saveHandler, 'destroy'),
            array($this->saveHandler, 'gc')
        );
        $this->setCookieParams();
    }

    /**
     * Set session cookie parameters
     *
     * @return void
     */
    protected function setCookieParams()
    {
        session_set_cookie_params(
            $this->params['cookie']['expire'],
            $this->params['cookie']['path'],
            $this->params['cookie']['domain'],
            $this->params['cookie']['secure'], 
            $this->params['cookie']['httpOnly']
        );
    }

    /**
     * Set session name
     * 
     * @param string $name session name
     *
     * @return void
     */
    public function setName($name = null)
    {
        if ($name == null) {
            $name = $this->params['cookie']['prefix'].$this->params['cookie']['name'];
        }
        $this->name = $name;
        session_name($name);
        return $this;
    }

    /**
     * Get name of the session
     * 
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = session_name();
        }
        return $this->name;
    }

    /**
     * Session start
     * 
     * @return void
     */
    public function start()
    {
        if (! $this->exists()) { // If another session_start() used before ?
            session_start();
        }
    }

    /**
     * Read Cookie and validate Meta Data
     * 
     * @return boolean
     */
    public function readSession()
    {
        $name = $this->getName();
        $cookie = (isset($this->cookie[$name])) ? $this->cookie[$name] : false;
        if ($cookie === false) {
            return false;
        }
        return true;
    }

    /**
     * Regenerate id
     *
     * Regenerate the session ID, using session save handler's
     * native ID generation Can safely be called in the middle of a session.
     *
     * @param bool $deleteOldSession whether to delete previous session data
     * @param int  $lifetime         max lifetime of session
     * 
     * @return string new session id
     */
    public function regenerateId($deleteOldSession = true, $lifetime = null)
    {
        session_regenerate_id((bool) $deleteOldSession);
        $storageLifetime = ($lifetime == null) ? $this->params['storage']['lifetime'] : $lifetime;
        $this->saveHandler->setLifetime($storageLifetime);

        return session_id(); // new session_id
    }

    /**
     * Does a session exist and is it currently active ?
     *
     * @return bool
     */
    public function exists()
    {
        if (session_status() == PHP_SESSION_ACTIVE && session_id()) {  // Session is active and session not empty.
            return true;
        }
        if (headers_sent()) {
            return true;
        }
        return false;
    }

    /**
     * Destroy the current session
     *
     * @return void
     */
    public function destroy()
    {
        if (! $this->exists()) {
            return;
        }
        session_destroy();
        if (! headers_sent()) {
            setcookie(
                $this->getName(),                 // session name
                '',                               // value
                $this->server['REQUEST_TIME'] - 42000, // TTL for cookie
                $this->params['cookie']['path'],
                $this->params['cookie']['domain'],
                $this->params['cookie']['secure'], 
                $this->params['cookie']['httpOnly']
            );
        }
    }

    /**
     * Fetch a specific item from the session array
     *
     * @param string $item   session key
     * @param string $prefix session key prefix
     * 
     * @return string
     */
    public function get($item, $prefix = '')
    {
        if ($item == 'session_id') {
            return session_id();
        }
        return ( ! isset($_SESSION[$prefix . $item])) ? false : $_SESSION[$prefix . $item];
    }

    /**
     * Add or change data in the $_SESSION
     * 
     * @param mixed  $new    key or array
     * @param string $newval value
     * @param string $prefix prefix
     * 
     * @return void
     */
    public function set($new = array(), $newval = '', $prefix = '')
    {
        if (is_string($new)) {
            $new = array($new => $newval);
        }
        if (sizeof($new) > 0) {
            foreach ($new as $key => $val) {
                $_SESSION[$prefix . $key] = $val;
            }
        }
    }

    /**
     * Returns all session data
     * 
     * @return array
     */
    public function getAll()
    {
        if (isset($_SESSION)) {
            return $_SESSION;
        }
        return array();
    }

    /**
     * Delete a session variable from the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $prefix sesison key prefix
     * 
     * @return void
     */
    public function remove($new = array(), $prefix = '')
    {
        if (is_string($new)) {
            $new = array($new => '');
        }
        if (sizeof($new) > 0) {
            foreach ($new as $key => $val) {
                $val = null;
                unset($_SESSION[$prefix . $key]);
            }
        }
        if (sizeof($_SESSION) == 0) {                   // When we want to unset() data we couldn't remove the last session key from storage.
            $this->saveHandler->destroy(session_id());  // This solution fix the issue.
        }
    }

    /**
     * Get the "now" time
     *
     * @return string
     */
    public function getTime()
    {
        $time = time();
        if (strtolower($this->params['locale']['timezone']) == 'gmt') {
            $now = time();
            $time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
        }
        return $time;
    }

    /**
     * Close session writer
     * 
     * @return void
     */
    public function close()
    {
        session_write_close();
    }
}