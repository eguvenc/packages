<?php

namespace Obullo\Authentication\User;

use Obullo\Authentication\Token;
use Obullo\Authentication\Recaller;
use Auth\Identities\AuthorizedUser;

use Obullo\Session\SessionInterface as Session;
use Obullo\Container\ContainerInterface as Container;
use Obullo\Authentication\Storage\StorageInterface as Storage;

/**
 * User Identity
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Identity extends AuthorizedUser implements IdentityInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

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
     * Keeps unique session login ids to destroy them
     * in destruct method.
     * 
     * @var array
     */
    protected $killSignal = array();

    /**
     * Constructor
     *
     * @param object $c       container
     * @param object $session storage
     * @param object $storage auth storage
     * @param object $params  auth config parameters
     */
    public function __construct(Container $c, Session $session, Storage $storage, array $params)
    {
        $this->c = $c;
        $this->params = $params;
        $this->session = $session;
        $this->storage = $storage;

        $this->initialize();

        if ($rememberToken = $this->recallerExists()) {   // Remember the user if recaller cookie exists
            
            $recaller = new Recaller($c, $storage, $c['auth.model'], $this, $params);
            $recaller->recallUser($rememberToken);

            $this->initialize();  // We need initialize again otherwise ignoreRecaller() does not work in Login class.
        }
        if ($this->params['middleware']['unique.session']) {
            
            register_shutdown_function(array($this, 'close'));
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
            $this->__isTemporary = 0;                   // Refresh memory key expiration time
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
        if (isset($this->__isAuthenticated) && $this->__isAuthenticated == 1) {
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
        $name = $this->params['login']['rememberMe']['cookie']['name'];
        $token = isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;

        if ($this->guest() && ctype_alnum($token) && strlen($token) == 32) {  // Check recaller cookie value is alfanumeric
            return $token;
        }
        return false;
    }

    /**
     * Returns to "1" if user authenticated on temporary memory block otherwise "0".
     *
     * @return boolean
     */
    public function isTemporary()
    {
        return $this->__isTemporary;
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
     * Check user is verified after succesfull login
     *
     * @return boolean
     */
    public function isVerified()
    {
        if (isset($this->__isVerified) && $this->__isVerified == 1) {
            return true;
        }
        return false;
    }

    /**
     * Checks new identity data available in storage.
     *
     * @return boolean
     */
    public function exists()
    {
        if (isset($this->__isAuthenticated)) {
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
        return isset($this->__time) ? $this->__time : null;
    }

    /**
     * Set all identity attributes
     *
     * @param array $attributes identity array
     *
     * @return $object identity
     */
    public function setArray(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
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
     * @return mixed false|string new password hash
     */
    public function getPasswordNeedsReHash()
    {
        return isset($this->__passwordNeedsRehash) ? $this->__passwordNeedsReHash['hash'] : false;
    }

    /**
     * Returns to "1" user if used remember me
     *
     * @return integer
     */
    public function getRememberMe()
    {
        return isset($this->__rememberMe) ? $this->__rememberMe : 0;
    }

    /**
     * Returns to remember token
     *
     * @return integer
     */
    public function getRememberToken()
    {
        return isset($this->__rememberToken) ? $this->__rememberToken : false;
    }

    /**
     * Sets authority of user to "0" don't touch to cached data
     *
     * @return void
     */
    public function logout()
    {
        $credentials = $this->storage->getCredentials('__permanent');
        $credentials['__isAuthenticated'] = 0;        // Sets memory auth to "0".

        $this->updateRememberToken();
        $this->storage->setCredentials($credentials, null, '__permanent');
    }

    /**
     * Logout User and destroy cached identity data
     *
     * @return void
     */
    public function destroy()
    {
        $this->updateRememberToken();
        $this->storage->deleteCredentials('__permanent');
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
            $rememberToken    = $this->c['cookie']->get($rememberMeCookie['name'], $rememberMeCookie['prefix']);

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
        $token = Token::getRememberToken($this->c['cookie'], $this->params);

        return $this->c['auth.model']->updateRememberToken($token, $credentials); // refresh rememberToken
    }

    /**
     * Removes rememberMe cookie from user browser
     *
     * @return void
     */
    public function forgetMe()
    {
        $cookie = $this->params['login']['rememberMe']['cookie']; // Delete rememberMe cookie if exists
        setcookie(
            $cookie['prefix'].$cookie['name'], 
            null,
            -1,
            $cookie['path'],
            $cookie['domain'],   //  Get domain from global config
            $cookie['secure'], 
            $cookie['httpOnly']
        );
    }

    /**
     * Kill authority of user using auth id
     * 
     * @param integer $loginId e.g: 87060e89
     * 
     * @return boolean
     */
    public function killSignal($loginId)
    {
        $this->killSignal[$loginId] = $loginId;
    }

    /**
     * Do finish operations
     * 
     * @return void
     */
    public function close()
    {
        if (empty($this->killSignal)) {
            return;
        }
        foreach ($this->killSignal as $loginId) {
            $this->storage->killSession($loginId);
        }
    }
}