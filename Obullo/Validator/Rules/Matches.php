<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;
use Psr\Http\Message\ServerRequestInterface as Request;

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
     * Constructor
     * 
     * @param Request $request request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Call next
     * 
     * @param Field $next object
     * 
     * @return object
     */
    public function __invoke(Field $next)
    {
        $field = $next;
        $value = $field->getValue();
        $params = $field->getParams();

        $matchField = isset($params[0]) ? $params[0] : '';

        if ($this->isValid($value, $matchField)) {
            return $next();
        }
        return false;
    }

    /**
     * Match one field to another
     * 
     * @param string $value      field value
     * @param string $matchField matched field name
     * 
     * @return bool
     */    
    public function isValid($value, $matchField)
    {   
        $matchField = $this->request->post($matchField);

        if (! $matchField) {
            return false;                
        }
        return ($value !== $matchField) ? false : true;
    }
}