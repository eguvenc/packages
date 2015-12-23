<?php

namespace Obullo\Validator;

/**
 * IsBool
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsBool
{
    /**
     * Is Boolean
     * 
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( is_bool($str) || $str == 0 || $str == 1 ) ? true : false;
    }
}