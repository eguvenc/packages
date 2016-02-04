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

        return $this->attributes[$id];
    }

    /**
     * Get the password column value
     *
     * @return mixed
     */
    public function getPassword()
    {
        $password = $this->getColumnPassword();

        return $this->attributes[$password];
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
        return $this->__rememberMe;
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
     * Dynamically access the user's attributes.
     *
     * @param string $key key
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : false;
    }

    /**
     * Dynamically set the user's attributes.
     *
     * @param string $key key
     * @param string $val value
     * 
     * @return mixed
     */
    public function __set($key, $val)
    {
        if ($this->__isAuthenticated == 1) {     // Check user has auth
            $this->storage->update($key, $val);  // then accept update operation
        }
        return $this->attributes[$key] = $val;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param string $key key
     * 
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param string $key key
     * 
     * @return void
     */
    public function __unset($key)
    {
        if ($this->__isAuthenticated == 1) {
            $this->storage->remove($key);
        }
        unset($this->attributes[$key]);
    }

}