<?php

namespace Obullo\Validator;

use Obullo\Validator\ValidatorInterface as Validator;

/**
 * Valid Email
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Email
{
    protected $dnsCheck = false;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(Validator $validator, $field, $params = array())
    {
        $validator = $field = null;
        $this->dnsCheck = isset($params[0]) ? (bool)$params[0] : false;
    }

    /**
     * Valid Email
     *
     * @param string $value email
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        $isValid = (filter_var($value, FILTER_VALIDATE_EMAIL)) === false ? false : true;

        if ($isValid && $this->dnsCheck) {
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