<?php

namespace Obullo\Authentication\Model;

use Obullo\Container\ServiceProviderInterface as Provider;

/**
 * User Provider Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UserInterface
{
     /**
     * Constructor
     * 
     * @param object $provider ServiceProviderInterface
     * @param object $params   Auth configuration & service configuration parameters
     */
    public function __construct(Provider $provider, array $params);

    /**
     * Execute sql query
     *
     * @param array $credentials user credentials
     * 
     * @return mixed boolean|array
     */
    public function query(array $credentials);
    
    /**
     * Recalled user sql query using remember cookie
     * 
     * @param string $token rememberMe token
     * 
     * @return array
     */
    public function recallerQuery($token);
    
    /**
     * Update remember token upon every login & logout requests
     * 
     * @param string $token       name
     * @param array  $credentials credentials
     * 
     * @return void
     */
    public function updateRememberToken($token, array $credentials);

}