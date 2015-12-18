<?php

namespace Obullo\Validator;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;

/**
 * Alpha
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Alpha
{
    /**
     * Alpha
     * 
     * @param string $str string
     *
     * @return bool
     */         
    public function isValid($str)
    {
        return ( ! preg_match("/^([-a-z0-9_\-])+$/i", $str)) ? false : true;
    }
}