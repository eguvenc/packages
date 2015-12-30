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
    protected $length;

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

        $this->length = isset($params[0]) ? (string)$params[0] : '0';

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
        if (! ctype_digit($this->length)) {
            return false;
        }
        return (mb_strlen($value) < $this->length) ? false : true;   
    }
}