<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * IsJson
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsJson
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
     * Is Json
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( ! is_object(json_decode($value))) ? false : true;
    }
}