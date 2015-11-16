<?php

namespace Obullo\Validator;

/**
 * Min Class
 * 
 * @category  Validator
 * @package   Min
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Min
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
        return (mb_strlen($str) < $val) ? false : true;
    }
}