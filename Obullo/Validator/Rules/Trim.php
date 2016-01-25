<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Trim
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Trim
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
        $value = trim($field->getValue());
        $field->setValue($value);
        
        return $next();
    }
}