<?php

namespace Obullo\Validator;

use Obullo\Validator\ValidatorInterface as Validator;

/**
 * Matches
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Matches
{
    /**
     * Request
     * 
     * @var object
     */
    protected $request;

    /**
     * Input element
     * 
     * @var string
     */
    protected $field;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(Validator $validator, $field, $params = array())
    {
        $params = null;
        $this->field = $field;
        $container = $validator->getContainer();
        $this->request = $container['request'];
    }

    /**
     * Match one field to another
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        $request = $this->request->all();

        if (! isset($request[$this->field])) {
            return false;                
        }
        return ($value !== $request[$this->field]) ? false : true;
    }
}