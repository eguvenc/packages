<?php

namespace Obullo\Validator\Rules;

/**
 * IsDecimal
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsDecimal extends AbstractRule
{
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