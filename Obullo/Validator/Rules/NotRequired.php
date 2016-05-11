<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Required
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NotRequired
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
        $value = $field->getValue();

        if (is_object($value) || is_null($value)) {
            return true;
        }
        if (is_string($value) && empty($value)) {
            return true;
        }
        if (is_array($value) && ($value == array())) {
            return true;
        }
        if (is_string($value) && ($value == '0')) {
            return true;
        }        
        if (is_string($value) && ($value == '')) {
            return true;
        }        
        if (is_float($value) && ($value == 0.0)) {
            return true;
        }        
        if (is_int($value) && ($value == 0)) {
            return true;
        }
        if (is_bool($value) && ($value == false)) {
            return true;
        }
        
        return $next($field);
    }

}
