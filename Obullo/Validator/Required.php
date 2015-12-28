<?php

namespace Obullo\Validator;

/**
 * Required
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Required
{
    /**
     * Empty or not
     * 
     * @param string $value value
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        return (empty($value) && $value != 0) ? false : true;
    }
}