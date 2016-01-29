<?php

namespace Obullo\Validator\Rules;

/**
 * Is Boolean
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsBool extends AbstractRule
{
    /**
     * Is Boolean
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return ( is_bool($value) || $value == 0 || $value == 1 ) ? true : false;
    }
}