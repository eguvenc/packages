<?php

namespace Obullo\Validator;

use Obullo\Container\ContainerInterface as Container;

/**
 * Matches
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Matches
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Container
     * 
     * @param Container $container contaienr
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

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
        $request = $this->c['request']->all();

        if (! isset($request[$field])) {
            return false;                
        }
        return ($str !== $request[$field]) ? false : true;
    }
}