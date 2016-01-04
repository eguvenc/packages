<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * IsDecimal
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsDecimal
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
        $params = $field->getParams();

        if ($this->isValid($value, $params)) {
            return $next();
        }
        return false;
    }

    /**
     * IsDecimal
     *
     * @param string $value value
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return is_numeric($value) && floor($value) != $value;
    }
}