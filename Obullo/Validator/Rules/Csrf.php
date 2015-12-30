<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;
use Obullo\Security\Csrf as CsrfClass;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Csrf form verify
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf
{
    protected $csrf;
    protected $field;
    protected $request;

    /**
     * Constructor
     * 
     * @param Csrf    $csrf    csrf
     * @param Request $request request
     */
    public function __construct(CsrfClass $csrf, Request $request)
    {
        $this->csrf = $csrf;
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
        $this->field = $next;
        $value = $this->field->getValue();

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
        if ($this->request->getMethod() == 'POST') {

            $inputName = $this->csrf->getTokenName();

            if (false == $this->request->post($inputName)) {

                $this->field->setFormMessage('OBULLO:VALIDATOR:CSRF:REQUIRED');

                return false;
            }
            $verify = $this->csrf->verify($this->request);

            if ($verify == false) {

                $this->field->setFormMessage('OBULLO:VALIDATOR:CSRF:INVALID');

                return false;
            }
            return true;
        }
    }
}