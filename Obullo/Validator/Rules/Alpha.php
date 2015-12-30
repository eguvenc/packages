<?php

namespace Obullo\Validator;

/**
 * Alpha ( Only letters )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Alpha
{
    /**
     * Alpha
     * 
     * @param string $value string
     *
     * @return bool
     */         
    public function isValid($value)
    {
        return (! ctype_alpha($value)) ? false : true;
    }
}