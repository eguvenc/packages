<?php

namespace Obullo\Authentication\User;

use Obullo\Authentication\Token;
use Obullo\Authentication\Recaller;

use Obullo\Authentication\AbstractIdentity;
use Obullo\Session\SessionInterface as Session;
use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Obullo\Authentication\Storage\StorageInterface as Storage;

/**
 * User Identity
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Identity extends AbstractIdentity
{
    /**
     * Auth configuration params
     * 
     * @var array
     */
    protected $params;

    /**
     * Session
     * 
     * @var object
     */
    protected $session;

    /**
     * Memory Storage
     *
     * @var object
     */
    protected $storage;

    /**
     * Request
     * 
     * @var object
     */
    protected $request;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Keeps unique session login ids to destroy them
     * in destruct method.
     * 
     * @var array
     */
    protected $killSignal = array();

    /**
     * Constructor
     *
     * @param object $container container
     * @param object $request   psr7 request
     * @param object $session   storage
     * @param object $storage   auth storage
     * @param object $params    auth config parameters
     */
    public function __construct(Container $container, Request $request, Session $session, Storage $storage, array $params)
    {
        $this->params = $params;
        $this->request = $request;
        $this->session = $session;
        $this->storage = $storage;
        $this->container = $container;

        $this->initialize();

        if ($rememberToken = $this->recallerExists()) {   // Remember the user if recaller cookie exists
            $recaller = new Recaller($container, $storage, $container->get('auth.model'), $this, $params);
            $recaller->recallUser($rememberToken);
            
            $this->initialize();  // We need initialize again otherwise ignoreRecaller() does not work in Login class.
        }
    }

    /**
     * Initializer
     *
     * @return void
     */
    public function initialize()
    {
        if ($this->attributes = $this->storage->getCredentials('__permanent')) {
            $this->setCredentials($this->attributes);
            return;
        }
        $this->attributes = $this->storage->getCredentials('__temporary');
    }

    /**
     * Check user has identity
     *
     * Its ok if returns to true otherwise false
     *
     * @return boolean
     */
    public function check()
    {      
        if ($this->get('__isAuthenticated') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Opposite of check() function
     *
     * @return boolean
     */
    public function guest()
    {
        if ($this->check()) {
            return false;
        }
        return true;
    }

    /**
     * Check recaller cookie exists 
     * 
     * WARNING : To test this function remove "Auth/Identifier" value from session 
     * or use "$this->user->identity->destroy()" method.
     *
     * @return string|boolean false
     */
    public function recallerExists()
    {
        if ($this->session->get('Auth/IgnoreRecaller') == 1) {
            $this->session->remove('Auth/IgnoreRecaller');
            return false;
        }
        $name  = $this->params['login']['rememberMe']['cookie']['name'];
        $cookies = $this->request->getCookieParams();
        $token = isset($cookies[$name]) ? $cookies[$name] : false;

        if ($this->guest() && ctype_alnum($token) && strlen($token) == 32) {  // Check recaller cookie value is alfanumeric
            return $token;
        }
        return false;
    }

    /**
     * Returns to 1 if user authenticated on temporary memory block otherwise 0.
     *
     * @return boolean
     */
    public function isTemporary()
    {
        return (bool)$this->get('__isTemporary');
    }

    /**
     * Set time to live
     * 
     * @param int $ttl expire
     * 
     * @return void
     */
    public function expire($ttl)
    {
        if ($this->check()) {
            $this->storage->update('__expire', time() + $ttl);
        } 
    }

    /**
     * Check identity is expired
     * 
     * @return boolean
     */
    public function isExpired()
    {
        if ($this->has('__expire') && $this->get('__expire') < time()) {
            return true;
        }
        return false;
    }

    /**
     * Move permanent identity to temporary block
     * 
     * @return void
     */
    public function makeTemporary() 
    {
        $this->storage->makeTemporary();
    }

    /**
     * Move temporary identity to permanent block
     * 
     * @return void
     */
    public function makePermanent() 
    {
        $this->storage->makePermanent();
    }

    /**
     * Checks new identity data available in storage.
     *
     * @return boolean
     */
    public function exists()
    {
        if ($this->has('__isAuthenticated') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Returns to unix microtime value.
     *
     * @return string
     */
    public function getTime()
    {
        return $this->get('__time');
    }

    /**
     * Get all identity attributes
     *
     * @return array
     */
    public function getArray()
    {
        if (is_array($this->attributes)) {
            ksort($this->attributes);
        }
        return $this->attributes;
    }

    /**
     * Get the password needs rehash array.
     *
     * @return boolean
     */
    public function getPasswordNeedsReHash()
    {
        return $this->has('__passwordNeedsRehash') ? true : false;
    }

    /**
     * Returns to "1" user if used remember me
     *
     * @return integer
     */
    public function getRememberMe()
    {
        return $this->has('__rememberMe') ? $this->get('__rememberMe') : 0;
    }

    /**
     * Returns to remember token
     *
     * @return integer
     */
    public function getRememberToken()
    {
        return $this->has('__rememberToken') ? $this->get('__rememberToken') : false;
    }

    /**
     * Returns to login id of user, its an unique id for each browsers e.g: 87060e89.
     * 
     * @return string|false
     */
    public function getLoginId()
    {
        if (! $this->exists()) {
            return false;
        }
        return $this->session->get('Auth/LoginId');
    }

    /**
     * Sets authority of user to "0" don't touch to cached data
     *
     * @return void
     */
    public function logout()
    {
        if ($this->check()) {
            $this->storage->update('__isAuthenticated', 0);
            $this->updateRememberToken();
            $this->removeSessionIdentifiers();
        }
    }

    /**
     * Destroy all identity data
     *
     * @param string $block block
     * 
     * @return void
     */
    public function destroy($block = '__permanent')
    {
        if ($block == '__permanent' && $this->guest()) {
            return;
        }
        $this->updateRememberToken();
        $this->storage->deleteCredentials($block);
        $this->removeSessionIdentifiers();
    }

    /**
     * Remove identifiers from session
     * 
     * @return void
     */
    protected function removeSessionIdentifiers()
    {
        $this->session->remove('Auth/LoginId');
        $this->session->remove('Auth/Identifier');
    }

    /**
     * Update temporary credentials
     * 
     * @param string $key key
     * @param string $val value
     * 
     * @return void
     */
    public function updateTemporary($key, $val)
    {
        if ($this->check()) {
            return;
        }
        $this->storage->update($key, $val, '__temporary');
    }

    /**
     * Update remember token if it exists in the memory and browser header
     *
     * @return int|boolean
     */
    public function updateRememberToken()
    {
        if ($this->getRememberMe() == 1) {  // If user checked rememberMe option

            $rememberMeCookie = $this->params['login']['rememberMe']['cookie'];
            $rememberToken    = $this->container->get('cookie')->get($rememberMeCookie['name'], $rememberMeCookie['prefix']);

            $credentials = [
                $this->params['db.identifier'] => $this->getIdentifier(),
                '__rememberToken' => $rememberToken
            ];
            $this->setCredentials($credentials);

            return $this->refreshRememberToken($credentials);
        }
    }

    /**
     * Refresh the rememberMe token
     *
     * @param array $credentials credentials
     *
     * @return int|boolean
     */
    public function refreshRememberToken(array $credentials)
    {
        $token = Token::getRememberToken($this->container->get('cookie'), $this->params);

        return $this->container->get('auth.model')->updateRememberToken($token, $credentials); // refresh rememberToken
    }

    /**
     * Removes rememberMe cookie from user browser
     *
     * @return void
     */
    public function forgetMe()
    {
        $this->container->get('cookie')->delete($this->params['login']['rememberMe']['cookie']['name']);  // Delete rememberMe cookie if exists
    }

    /**
     * Public function
     * 
     * Validate a user against the given credentials.
     * 
     * @param array $credentials user credentials
     * 
     * @return bool
     */
    public function validate(array $credentials)
    {
        $plain = $credentials[$this->params['db.password']];

        return $this->container->get('password')->verify($plain, $this->getPassword());
    }

    /**
     * Kill authority of user using auth id
     * 
     * @param integer $loginId e.g: 87060e89
     * 
     * @return boolean
     */
    public function kill($loginId)
    {
        $this->storage->killSession($loginId);
    }

}