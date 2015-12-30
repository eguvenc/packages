<?php

namespace Obullo\Validator;

/**
 * Trim
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Trim
{
    /**
     * Trim
     * 
     * @param string $val value
     * 
     * @return bool
     */    
    public function func($val)
    {
        return trim($val);
    }
}