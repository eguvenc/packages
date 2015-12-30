<?php

namespace Obullo\Validator;

/**
 * IsDecimal
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class IsDecimal
{
    /**
     * Params
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(ValidatorInterface $validator, $field, $params = array())
    {
        $validator = $field = null;
        $this->params = $params;
    }

    /**
     * IsDecimal
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {
        if ($params = explode(',', $this->params[0])) {

            if (isset($params[1])) {
                
                $params[0] = $params[0] - $params[1];

                if (preg_match('/^\d{1,'.$params[0].'}(?:\.\d{1,'.$params[1].'})?$/', $value)) {
                    return true;
                }
            }
        }
        return false;
    }
}