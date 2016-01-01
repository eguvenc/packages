<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * IsNumeric
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsNumeric
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
        $field = $next;
        $value = $field->getValue();

        if ($this->isValid($value)) {
            return $next();
        }
        return false;
    }
    
    /**
     * Minimum length
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( ! is_numeric($value)) ? false : true;
    }
}