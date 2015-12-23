<?php

namespace Obullo\Validator;

/**
 * Min
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Min
{
    /**
     * Minimum length
     * 
     * @param string $str string
     * @param string $val value
     * 
     * @return bool
     */    
    public function isValid($str, $val)
    {
        if (preg_match('/[^0-9]/', $val)) {
            return false;
        }
        return (mb_strlen($str) < $val) ? false : true;
    }
}