<?php

namespace Obullo\Validator;

/**
 * IsDecimal Class
 * 
 * @category  Validator
 * @package   IsDecimal
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class IsDecimal
{
    /**
     * IsDecimal
     * 
     * @param string $str string
     * @param string $val value
     * 
     * @return bool
     */    
    public function isValid($str, $val)
    {
        if ($params = explode(',', $val)) {
            if (isset($params[1])) {
                $params[0] = $params[0] - $params[1];

                if (preg_match('/^\d{1,'.$params[0].'}(?:\.\d{1,'.$params[1].'})?$/', $str)) {
                    return true;
                }
            }
        }
        return false;
    }
}