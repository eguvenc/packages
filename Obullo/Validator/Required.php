<?php

namespace Obullo\Validator;

/**
 * Required Class
 * 
 * @category  Validator
 * @package   Required
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Required
{
    /**
     * Empty or not
     * 
     * @param string $val value
     * 
     * @return bool
     */    
    public function isValid($val)
    {
        return (empty($val) &&  $val != 0) ? false : true;
    }
}