<?php

namespace Obullo\Validator;

/**
 * Md5
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Md5
{
    /**
     * Md5 str
     * 
     * @param string $val value
     * 
     * @return bool
     */    
    public function func($val)
    {
        return md5($val);
    }
}