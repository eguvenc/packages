<?php

namespace Obullo\Tests;

use DateTime;
use Interop\Container\ContainerInterface as Container;

/**
 * Test user
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestUser
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Errors
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Constructor
     * 
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $container->get('user')->identity->initialize();
    }

    /**
     * Login
     * 
     * @param array $options login options (rememberMe = 1, regenerateSessionId = true)
     * 
     * @return void
     */
    public function login($options = array())
    {
        $this->attempt($options);

        if ($this->hasError()) {
            TestOutput::error($this->getErrors());
        }
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
        $credentials = $this->container->get('config')->get('tests')['login']['credentials'];

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

    /**
     * Logout test user
     * 
     * @return void
     */
    public function logout()
    {
        $user = $this->container->get('user');
        $user->identity->logout();
    }

    /**
     * Destroy identity
     * 
     * @return void
     */
    public function destroy()
    {
        $user = $this->container->get('user');
        $user->identity->destroy();
        $this->container->get('session')->destroy();  // Kill sessions.
    }

    /**
     * Call user class methods
     * 
     * @param string $key key
     * 
     * @return object
     */
    public function __get($key)
    {
        return $this->container->get('user')->{$key};
    }

}