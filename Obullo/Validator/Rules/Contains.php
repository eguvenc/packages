<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Contais - usage : contains(foo,baz,bar)
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Contains
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
        $param  = (string)$field->getRule()->getParam(0, '0');
        $values = explode(",", $param);

        if (is_array($values) && ! empty($values)) {
            if (is_string($field->getValue()) && in_array($field->getValue(), $values)) {
                return $next($field);
            }
        }
        return false;
    }
}