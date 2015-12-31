<?php

namespace Obullo\Validator\Rules;

use Obullo\Security\CsrfInterface;
use Obullo\Validator\FieldInterface as Field;
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
    protected $request;

    /**
     * Constructor
     * 
     * @param Csrf    $csrf    csrf
     * @param Request $request request
     */
    public function __construct(CsrfInterface $csrf, Request $request)
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
        $field = $next;
        if ($this->isValid($field)) {
            return $next();
        }
        return false;
    }

    /**
     * Check csrf code
     * 
     * @param Field $field field object
     * 
     * @return boolean
     */
    public function isValid(Field $field)
    {
        if ($this->request->getMethod() == 'POST') {

            if (! $this->csrf->verify($this->request)) {

                $code = $this->csrf->getErrorCode();
                
                switch ($code) {
                case 00:
                    $field->setMessage('OBULLO:VALIDATOR:CSRF:REQUIRED');
                    break;
                case 01:
                    $field->setMessage('OBULLO:VALIDATOR:CSRF:INVALID');
                    break;
                }
                return false;
            }

            return true;
        }
    }
}