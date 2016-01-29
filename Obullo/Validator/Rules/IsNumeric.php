<?php

namespace Obullo\Validator\Rules;

/**
 * IsNumeric
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsNumeric extends AbstractRule
{
    /**
     * Minimum length
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( ! is_numeric($value)) ? false : true;
    }
}