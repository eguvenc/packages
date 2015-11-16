<?php

namespace Obullo\Cookie;

/**
 * Cookie Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface CookieInterface
{
    /**
     * Set cookie name
     * 
     * @param string $name cookie name
     * 
     * @return object
     */
    public function name($name);
    
    /**
     * Set cookie value
     * 
     * @param string $value value
     * 
     * @return object
     */
    public function value($value = '');

    /**
     * Set cookie expire in seconds
     * 
     * @param integer $expire seconds
     * 
     * @return object
     */
    public function expire($expire = 0);

    /**
     * Set cookie domain name
     * 
     * @param string $domain name
     * 
     * @return void
     */
    public function domain($domain = '');

    /**
     * Set cookie path
     * 
     * @param string $path name
     * 
     * @return object
     */
    public function path($path = '/');

    /**
     * Set secure cookie
     * 
     * @param boolean $bool true or false
     * 
     * @return object
     */
    public function secure($bool = false);

    /**
     * Make cookie available just for http. ( No javascript )
     * 
     * @param boolean $bool true or false
     * 
     * @return object
     */
    public function httpOnly($bool = false);

    /**
     * Set a cookie prefix
     * 
     * @param string $prefix prefix
     * 
     * @return object
     */
    public function prefix($prefix = '');

    /**
     * Set cookie
     *
     * Accepts six parameter, or you can submit an associative
     * array in the first parameter containing all the values.
     * 
     * @param string $name  cookie name
     * @param string $value cookie value
     * 
     * @return array
     */
    public function set($name = null, $value = null);

    /**
     * Get cookie
     * 
     * @param string $key    cookie key
     * @param string $prefix cookie prefix
     * 
     * @return string sanizited cookie
     */
    public function get($key, $prefix = '');

    /**
    * Delete a cookie
    *
    * @param string $name   cookie
    * @param string $prefix custom prefix
    * 
    * @return void
    */
    public function delete($name = null, $prefix = null);

}