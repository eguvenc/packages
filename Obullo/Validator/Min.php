<?php

namespace Obullo\Validator;

use Obullo\Validator\ValidatorInterface as Validator;

/**
 * Min
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Min
{
    protected $length;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(Validator $validator, $field, $params = array())
    {
        $validator = $field = null;
        $this->length = isset($params[0]) ? (string)$params[0] : '0';
    }

    /**
     * Minimum length
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        if (! ctype_digit($this->length)) {
            return false;
        }
        return (mb_strlen($value) < $this->length) ? false : true;   
    }
}