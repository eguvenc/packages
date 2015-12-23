<?php

namespace Obullo\Validator;

/**
 * IsDecimal
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
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