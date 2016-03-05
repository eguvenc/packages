<?php

namespace Obullo\Http\Tests;

/**
 * LoginTrait for Http based tests.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
trait LoginTrait
{
    /**
     * Create new login request to your login web service
     * 
     * @return void|string
     */
    protected function newLoginRequest()
    {
        $credentials = $this->config->load('tests')['login']['credentials'];

        if ($this->user->identity->guest()) {

            $authResult = $this->user->login->attempt(
                [
                    'db.identifier' => $credentials['username'], 
                    'db.password'   => $credentials['password'],
                ]
            );
            $results = $authResult->getArray();

            if ($results['code'] < 1 && $results['code'] != -3) {
                foreach ($results['messages'] as $error) {
                    $this->setError(trim($error, "."). ", code: ". $results['code']. ", identifier: ".$results['identifier'].".", "Authentication");
                }
            }
        }
    }
}