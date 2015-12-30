<?php

namespace Obullo\Validator;

/**
 * AlfaDash ( Only letters & underscore & dash )
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AlfaDash
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
        return ( ! preg_match("/^([-a-z_\-])+$/i", $value)) ? false : true;
    }
}