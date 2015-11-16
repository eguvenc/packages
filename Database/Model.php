<?php

namespace Obullo\Database;

use Controller;

/**
 * Model Class ( Default Model )
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Model
{
    /**
     * Container
     * 
     * @var object
     */
    private $__container;
    
    /**
     * Controller loader
     * 
     * @param string $key class name
     * 
     * @return void
     */
    public function __get($key)
    {
        if ($this->__container == null) {
            global $c;
            $this->__container = &$c;
        }
        if ($key == 'c') {
            return $this->__container;
        }
        return $this->__container[$key];
    }
}