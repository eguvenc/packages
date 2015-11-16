<?php

namespace Obullo\Validator;

/**
 * IsNumeric Class
 * 
 * @category  Validator
 * @package   IsNumeric
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class IsNumeric
{
    /**
     * Minimum length
     * 
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( ! is_numeric($str)) ? false : true;
    }
}