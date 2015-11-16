<?php

namespace Obullo\Validator;

/**
 * Max Class
 * 
 * @category  Validator
 * @package   Max
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Max
{
    /**
     * Minimum length
     * 
     * @param string $str string
     * @param string $val value
     * 
     * @return bool
     */    
    public function isValid($str, $val)
    {   
        if (preg_match('/[^0-9]/', $val)) {
            return false;
        }
        return (mb_strlen($str) > $val) ? false : true;   
    }
}