<?php

namespace Obullo\Validator;

/**
 * Valid Email
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Email
{
    /**
     * Valid Email
     *
     * @param string  $str email
     * @param boolean $dns dns check
     * 
     * @return bool
     */    
    public function isValid($str, $dns = false)
    {
        $isValid = (filter_var($str, FILTER_VALIDATE_EMAIL)) === false ? false : true;

        if ($isValid && $dns) {
            $username = null;
            $domain   = null;
            list($username, $domain) = explode('@', $str);
            if (! checkdnsrr($domain, 'MX')) {
                return false;
            }
            return true;
        }
        return $isValid;
    }
}