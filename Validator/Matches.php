<?php

namespace Obullo\Validator;

/**
 * Matches Class
 * 
 * @category  Validator
 * @package   Matches
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Matches
{
    /**
     * Match one field to another
     * 
     * @param string $str   string
     * @param string $field field
     * 
     * @return bool
     */    
    public function isValid($str, $field)
    {   
        if ( ! isset($_REQUEST[$field])) {
            return false;                
        }
        return ($str !== $_REQUEST[$field]) ? false : true;
    }
}