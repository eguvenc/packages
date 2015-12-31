<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Validate Email
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Email
{
    /**
     * Call next
     * 
     * @param Field $next object
     * 
     * @return object
     */
    public function __invoke(Field $next)
    {
        $field  = $next;
        $value  = $field->getValue();
        $params = $field->getParams();

        $dnsCheck = isset($params[0]) ? (bool)$params[0] : false;

        if ($this->isValid($value, $dnsCheck)) {
            return $next();
        }
        return false;
    }

    /**
     * Valid Email
     *
     * @param string  $value    email
     * @param boolean $dnsCheck check dns
     * 
     * @return bool
     */    
    public function isValid($value, $dnsCheck = false)
    {
        $isValid = (filter_var($value, FILTER_VALIDATE_EMAIL)) === false ? false : true;

        if ($isValid && $dnsCheck) {
            $username = null;
            $domain   = null;
            list($username, $domain) = explode('@', $value);
            if (! checkdnsrr($domain, 'MX')) {
                return false;
            }
            return true;
        }
        
        return $isValid;
    }
}