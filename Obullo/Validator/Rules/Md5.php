<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Md5
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Md5
{
    /**
     * Md5
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