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
     * @param string $val value
     * 
     * @return bool
     */    
    public function isValid($val)
    {
        return (empty($val) &&  $val != 0) ? false : true;
    }
}