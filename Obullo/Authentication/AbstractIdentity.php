<?php

namespace Obullo\Authentication;

/**
 * Abstract Identity
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractIdentity implements IdentityInterface
{
    /**
     * Credentials
     * 
     * @var array
     */
    protected $attributes = array();

    /**
     * Get the identifier column value
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        $id = $this->getColumnIdentifier();

        return $this->get($id);
    }

    /**
     * Get the password column value
     *
     * @return mixed
     */
    public function getPassword()
    {
        $password = $this->getColumnPassword();

        return $this->get($password);
    }
    
    /**
     * Set credentials
     * 
     * @param array $credentials credentials
     * 
     * @return void
     */
    public function setCredentials($credentials = array())
    {
        $this->attributes = $credentials;
    }

    /**
     * Get identifier column
     * 
     * @return string
     */
    public function getColumnIdentifier()
    {
        return $this->container->get('user.params')['db.identifier'];
    }

    /**
     * Get password column
     * 
     * @return string
     */
    public function getColumnPassword()
    {
        return $this->container->get('user.params')['db.password'];
    }

    /**
     * Returns to "1" user if used remember me
     * 
     * @return integer
     */
    public function getRememberMe() 
    {
        return $this->get('__rememberMe');
    }
    
    /**
     * Get all attributes
     * 
     * @return array
     */
    public function getArray()
    {
        return $this->attributes;
    }

    /**
     * Get a value from identity data.
     *
     * @param string $key key
     * 
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : false;
    }

    /**
     * Set a value to identity data.
     *
     * @param string $key key
     * @param string $val value
     * 
     * @return mixed
     */
    public function set($key, $val)
    {
        if ($this->get('__isAuthenticated') == 1) {     // Check user has auth
            $this->storage->update($key, $val);  // then accept update operation
        }
        return $this->attributes[$key] = $val;
    }

    /**
     * Check if a value is exists on the identity data.
     *
     * @param string $key key
     * 
     * @return bool
     */
    public function has($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Remove a value from identity data.
     *
     * @param string $key key
     * 
     * @return void
     */
    public function remove($key)
    {
        if ($this->get('__isAuthenticated') == 1) {
            $this->storage->remove($key);
        }
        unset($this->attributes[$key]);
    }

}