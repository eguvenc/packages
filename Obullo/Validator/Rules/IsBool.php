<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Is Boolean
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsBool
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
     * Is Boolean
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( is_bool($value) || $value == 0 || $value == 1 ) ? true : false;
    }
}