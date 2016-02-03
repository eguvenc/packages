<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Exact
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Exact
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
        $length = (string)$field->getRule()->getParam(0, '0');

        if (! ctype_digit($length)) {
            return false;
        }
        return (mb_strlen($field->getValue()) != $length) ? false : $next($field);
    }
}
