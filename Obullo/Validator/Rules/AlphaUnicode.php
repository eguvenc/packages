<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Alpha Unicode (https://github.com/vlucas/valitron/issues/79)
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AlphaUnicode
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
        if (preg_match("/^[\pL]+$/u", $field->getValue())) {

            return $next($field);
        }
        return false;
    }
}
