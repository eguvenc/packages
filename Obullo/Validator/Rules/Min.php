<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Min
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Min
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

        $length = isset($params[0]) ? (string)$params[0] : '0';

        if ($this->isValid($value, $length)) {
            return $next();
        }
        return false;
    }

    /**
     * Minimum length
     * 
     * @param string $value  value
     * @param int    $length length
     * 
     * @return bool
     */    
    public function isValid($value, $length = '0')
    {   
        if (! ctype_digit($length)) {
            return false;
        }
        return (mb_strlen($value) < $length) ? false : true;   
    }
}