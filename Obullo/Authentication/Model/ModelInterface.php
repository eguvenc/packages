<?php

namespace Obullo\Authentication\Model;

use Interop\Container\ContainerInterface as Container;

/**
 * Model Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ModelInterface
{
     /**
     * Constructor
     * 
     * @param object $container container
     * @param object $params    Auth configuration & service configuration parameters
     */
    public function __construct(Container $container, array $params);

    /**
     * Execute query
     *
     * @param array $credentials user credentials
     * 
     * @return mixed boolean|array
     */
    public function query(array $credentials);
    
    /**
     * Recalled user query using remember cookie
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