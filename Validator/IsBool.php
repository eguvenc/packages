<?php

namespace Obullo\Validator;

/**
 * IsBool Class
 * 
 * @category  Validator
 * @package   IsBool
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class IsBool
{
    /**
     * Is Boolean
     * 
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( is_bool($str) || $str == 0 || $str == 1 ) ? true : false;
    }
}