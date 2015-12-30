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
    protected $dnsCheck = false;

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

        $dns = isset($params[0]) ? (bool)$params[0] : false;
        $this->setDnsCheck($dns);

        if ($this->isValid($value)) {

            return $next();
        }
        return false;
    }

    /**
     * Enabled / disable dns option
     * 
     * @param boolean $enabled dns option
     * 
     * @return void
     */
    public function setDnsCheck($enabled = true)
    {
        $this->dnsCheck = $enabled;
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