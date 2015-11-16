<?php

namespace Obullo\Authentication\Adapter;

/**
 * Adapter Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface AdapterInterface
{
    /**
     * Performs an authentication attempt
     *
     * @param array   $credentials username and plain password
     * @param boolean $login       whether to authenticate user
     * 
     * @return object authResult
     */
    public function login(array $credentials, $login = true);

    /**
     * This method is called to attempt an authentication. Previous to this
     * call, this adapter would have already been configured with all
     * necessary information to successfully connect to "memory storage". 
     * If memory login fail it will connect to "database table" and run sql 
     * query to find a record matching the provided identity.
     *
     * @param array   $credentials username and plain password
     * @param boolean $login       whether to authenticate user
     * 
     * @return object
     */
    public function authenticate(array $credentials, $login = true);

     /**
     * Set identities data to AuthorizedUser object
     * 
     * @param array $credentials         username and plain password
     * @param array $resultRowArray      success auth query user data
     * @param array $passwordNeedsRehash marks attribute if password needs rehash
     *
     * @return object
     */
    public function generateUser(array $credentials, $resultRowArray, $passwordNeedsRehash = array());
}