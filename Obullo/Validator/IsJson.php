<?php

namespace Obullo\Validator;

/**
 * IsJson Class
 * 
 * @category  Validator
 * @package   IsJson
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class IsJson
{
    /**
     * is Json
     * 
     * @param string $str string
     * 
     * @return bool
     */    
    public function isValid($str)
    {
        return ( ! is_object(json_decode($str))) ? false : true;
    }
}