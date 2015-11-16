<?php

namespace Obullo\Authentication;

use Obullo\Utils\Random;
use Obullo\Cookie\CookieInterface as Cookie;

/**
 * Token generator
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Token
{
    /**
     * Run cookie reminder
     *
     * @param object $cookie CookieInterface
     * @param array  $params parameters
     * 
     * @return string token
     */
    public static function getRememberToken(Cookie $cookie, array $params)
    {
        $cookieParams = $params['login']['rememberMe']['cookie'];

        $token = $cookieParams['value'] = Random::generate('alnum', 32);
        $cookie->set($cookieParams);
        return $token;
    }
}