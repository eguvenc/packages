<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Max
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Max
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
        $length = '0';
        if ($params = $field->getParams()) {
            $length = (string)$params[0];
        }
        if (mb_strlen($field->getValue()) > $length) {
            return false;
        }
        return $next($field);
    }
}