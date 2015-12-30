<?php

namespace Obullo\Validator;

/**
 * Alfa numeric ( Only letters & numbers )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Alnum
{
    /**
     * Alpha numeric
     * 
     * @param string $value string
     *
     * @return bool
     */         
    public function isValid($value)
    {
        return (! ctype_alnum($value)) ? false : true;
    }
}