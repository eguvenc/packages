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
     * http://stackoverflow.com/questions/6772603/php-check-if-number-is-decimal
     * 
     * @param string $value  string
     * @param array  $params array
     * 
     * @return bool
     */    
    public function isValid($value, $params = array())
    {
        if ($params = explode(',', $params[0])) {

            if (isset($params[1])) {
                
                $params[0] = $params[0] - $params[1];

                if (preg_match('/^\d{1,'.$params[0].'}(?:\.\d{1,'.$params[1].'})?$/', $value)) {
                    return true;
                }
            }
        }
        return false;
    }
}