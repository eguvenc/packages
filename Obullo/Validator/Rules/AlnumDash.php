<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * AlnumDash ( Only letters & numbers & underscore & dash )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AlnumDash
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
     * AlphaDash
     * 
     * @param string $value string
     *
     * @return bool
     */         
    public function isValid($value)
    {
        return ( ! preg_match("/^([-a-z0-9_\-])+$/i", $value)) ? false : true;
    }
}