<?php

namespace Obullo\Validator;

/**
 * IsJson
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsJson
{
    /**
     * is Json
     * 
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( ! is_object(json_decode($str))) ? false : true;
    }
}