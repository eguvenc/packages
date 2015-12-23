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
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( ! is_numeric($str)) ? false : true;
    }
}