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
     * @param Field    $field object
     * @param Callable $next  object
     * 
     * @return object
     */
    public function __invoke(Field $field, Callable $next)
    {
        $dnsCheck = false;
        if ($params = $field->getParams()) {
            $dnsCheck = (bool)$params[0];
        }
        $isValid = (filter_var($field->getValue(), FILTER_VALIDATE_EMAIL)) === false ? false : true;

        if ($isValid) {

            if ($dnsCheck) {
                $username = null;
                $domain   = null;
                list($username, $domain) = explode('@', $field->getValue());
                if (! checkdnsrr($domain, 'MX')) {
                    return false;
                }
            }
            return $next($field);
        }
        return false;
    }
}