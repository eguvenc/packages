<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Alfa numeric ( Only letters & numbers )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Alnum
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
     * Alpha numeric
     * 
     * @param string $value string
     *
     * @return bool
     */         
    public function isValid($value)
    {
        return (! ctype_alnum($value)) ? false : true;
    }
}