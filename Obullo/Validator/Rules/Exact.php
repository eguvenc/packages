<?php

namespace Obullo\Validator\Rules;

/**
 * Exact
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Exact extends AbstractRule
{
    /**
     * Exact length
     * 
     * @param string $value value
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        $length = '0';
        if ($params = $this->getField()->getParams()) {
            $length = (string)$params[0];
        }
        if (! ctype_digit($length)) {
            return false;
        }
        return (mb_strlen($value) != $length) ? false : true;   
    }
}
