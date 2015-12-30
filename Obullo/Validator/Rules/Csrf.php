<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;

/**
 * Csrf form verify
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf
{
    protected $csrf;
    protected $request;
    protected $validator;
    protected $translator;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct()
    {
        // $this->csrf = $container['csrf'];
        // $this->request = $container['request'];
        // $this->translator = $container['translator'];
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
        $field  = $next;
        $value  = $field->getValue();
        $params = $field->getParams();

        if ($this->isValid($value)) {

            return $next();
        }
        return false;
    }

    /**
     * Csrf code check
     *
     * @return bool
     */         
    public function isValid()
    {
        return true;

        if ($this->request->getMethod() == 'POST') {

            $inputName = $this->csrf->getTokenName();

            if (false == $this->request->post($inputName)) {

                $this->setErrorMessage(
                    $this->translator->get(
                        'OBULLO:VALIDATOR:CSRF:REQUIRED',
                        $inputName
                    )
                );
                return false;
            }
            $verify = $this->csrf->verify($this->request);

            if ($verify == false) {
                $this->setErrorMessage('OBULLO:VALIDATOR:CSRF:INVALID');
                return false;
            }
            return true;
        }
    }
}