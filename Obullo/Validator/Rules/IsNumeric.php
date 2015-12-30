<?php

namespace Obullo\Validator;

/**
 * IsNumeric
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsNumeric
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