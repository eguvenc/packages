<?php

namespace Obullo\Tests;

use Interop\Container\ContainerInterface as Container;

/**
 * Login class for tests. 
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestLogin
{
    /**
     * Errors
     * 
     * @var array
     */
    protected $errors = array();

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
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Create new login request
     *
     * @param array $options login options (rememberMe = 1, regenerateSessionId = true)
     * 
     * @return void|string
     */
    public function attempt($options = array())
    {
        $credentials = $this->container->get('config')->load('tests')['login']['credentials'];

        $user = $this->container->get('user');

        if ($user->identity->guest()) {

            $authResult = $user->login->attempt(
                [
                    'db.identifier' => $credentials['username'], 
                    'db.password'   => $credentials['password'],
                ],
                $options
            );
            $results = $authResult->getArray();

            if ($results['code'] < 1 && $results['code'] != -3) {

                foreach ($results['messages'] as $error) {

                    $this->setError(trim($error, "."). ", code: ". $results['code']. ", identifier: ".$results['identifier'].".", "Authentication");
                }
            }
        }
    }

    /**
     * Set login errors
     * 
     * @param string $error error
     *
     * @return void
     */
    public function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Check login has error
     * 
     * @return boolean
     */
    public function hasError()
    {
        return empty($this->errors) ? false : true;
    }

    /**
     * Returns to error array
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}