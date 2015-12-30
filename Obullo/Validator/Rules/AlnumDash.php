<?php

namespace Obullo\Validator;

/**
 * AlnumDash ( Only letters & numbers & underscore & dash )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AlnumDash
{
    /**
     * AlphaDash
     * 
     * @param string $value string
     *
     * @return bool
     */         
    public function isValid($value)
    {
        return ( ! preg_match("/^([-a-z0-9_\-])+$/i", $value)) ? false : true;
    }
}