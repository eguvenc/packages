<?php

namespace Obullo\Authentication\User;

use Obullo\Authentication\AuthResult;
use Interop\Container\ContainerInterface as Container;
use Obullo\Authentication\Storage\StorageInterface as Storage;

use Event\LoginEvent;
use Event\LoginResultListener;

/**
 * Login
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Login
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     *
     * @param object $container container
     * @param object $storage   storage
     * @param array  $params    Auth config parameters
     */
    public function __construct(Container $container, Storage $storage, array $params)
    {
        $this->params = $params;
        $this->container = $container;
        $this->storage = $storage;
    }

    /**
     * Start the Login Operation ( validate, authenticate, set failure object )
     * 
     * @param array   $credentials user data
     * @param boolean $rememberMe  remember me switch
     * 
     * @return object AuthResult object
     */
    public function attempt(array $credentials, $rememberMe = false)
    {
        $this->ignoreRecaller();  // Ignore recaller if user has remember cookie
        
        $credentials['__rememberMe'] = ($rememberMe) ? 1 : 0;

        $credentials = $this->formatCredentials($credentials);

        if ($credentials == false) {

            $message = sprintf(
                'Login attempt requires "%s" and "%s" credentials.', 
                $credentials['db.identifier'],
                $credentials['db.password']
            );
            return new AuthResult(
                AuthResult::FAILURE, 
                null,
                $message
            );
        }
        /**
         * Create AuthResult Object
         */
        return $this->createResults($credentials);
    }

    /**
     * Returns to true if remember me cookie exists
     * 
     * @return boolean
     */
    public function hasRememberMe()
    {
        $name = $this->params['login']['rememberMe']['cookie']['name'];

        $cookies = $this->container->get('request')->getCookieParams();

        return isset($cookies[$name]) ? $cookies[$name] : false;
    }

    /**
     * Remove recaller cookie and ignore recaller
     * functionality.
     * 
     * @return void
     */
    public function ignoreRecaller()
    {
        if ($this->hasRememberMe()) {
            $this->container->get('session')->set('Auth/IgnoreRecaller', 1);
        }
    }

    /**
     * Combine credentials with real column names
     * 
     * @param array $credentials id & password data
     * 
     * @return boolean
     */
    public function formatCredentials(array $credentials)
    {   
        $i = $this->container->get('user.params')['db.identifier'];
        $p = $this->container->get('user.params')['db.password'];

        if (isset($credentials[$i]) && isset($credentials[$p])) {
            
            return $credentials;

        } elseif (isset($credentials['db.identifier']) && isset($credentials['db.password'])) {
            
            $credentials[$i] = $credentials['db.identifier'];
            $credentials[$p] = $credentials['db.password'];
            return $credentials;
        }
        return false;
    }

    /**
     * Create login attemtp and returns to auth result object
     * 
     * @param array $credentials login credentials
     * 
     * @return object AuthResult
     */
    protected function createResults(array $credentials)
    {
        /**
         * Login Query
         * 
         * @var object
         */
        $authResult = $this->container->get('auth.adapter')->login($credentials);

        /**
         * Generate User Identity
         */
        $this->container->get('auth.identity')->initialize();

        /**
         * Create event
         */
        $event = new LoginEvent($this->container);

        /**
         * Event variables
         */
        $name    = $event->getName();
        $emitter = $event->getEmitter();

        /**
         * Event listener
         */
        $emitter->addListener($name, new LoginResultListener);

        /**
         * Emit data
         */
        $emitter->emit($name, $authResult);

        /**
         * Event result returns multiple array response but we use one.
         */
        return $authResult;
    }

    /**
     * Validate a user's credentials without authenticate the user.
     *
     * @param array $credentials identities
     * 
     * @return bool
     */
    public function validate(array $credentials = array())
    {        
        $credentials = $this->formatCredentials($credentials);

        return $this->container->get('auth.adapter')->authenticate($credentials, false);
    }
    
    /**
     * Returns to all sessions of valid user
     *
     * One user may have multiple sessions on different 
     * devices.
     * 
     * @return array
     */
    public function getUserSessions()
    {
        return $this->storage->getUserSessions();
    }

}