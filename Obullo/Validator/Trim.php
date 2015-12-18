<?php

namespace Obullo\Validator;

/**
 * Trim
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Trim
{
    /**
     * Trim str
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