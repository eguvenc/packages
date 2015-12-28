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
     * Is Json
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( ! is_object(json_decode($value))) ? false : true;
    }
}