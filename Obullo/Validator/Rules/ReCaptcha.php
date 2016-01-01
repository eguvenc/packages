<?php

namespace Obullo\Validator\Rules;

use Obullo\Captcha\CaptchaInterface;
use Obullo\Validator\FieldInterface as Field;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ReCaptcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptcha
{
    protected $request;
    protected $recaptcha;

    /**
     * Constructor
     * 
     * @param Request $request   request
     * @param Captcha $recaptcha recaptcha
     */
    public function __construct(Request $request, CaptchaInterface $recaptcha)
    { 
        $this->request = $request;
        $this->recaptcha = $recaptcha;
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
     * Check recaptcha
     * 
     * @param object $field field
     * 
     * @return bool
     */    
    public function isValid(Field $field)
    {  
        if ($this->request->isPost()) {

            $value = $this->request->post('g-recaptcha-response');

            if (false == $this->recaptcha->result($value)->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return true;
        }
        return false;
    }
}