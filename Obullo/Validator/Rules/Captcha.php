<?php

namespace Obullo\Validator\Rules;

use Obullo\Captcha\CaptchaInterface;
use Obullo\Validator\FieldInterface as Field;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Captcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Captcha
{
    protected $request;
    protected $captcha;

    /**
     * Constructor
     * 
     * @param Request $request request
     * @param Captcha $captcha captcha
     */
    public function __construct(Request $request, Captcha $captcha)
    { 
        $this->request = $request;
        $this->captcha = $captcha;
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

        if ($this->isValid($value, $field)) {
            return $next();
        }
        return false;
    }

    /**
     * Check captcha
     * 
     * @param string $value string
     * @param object $field field
     * 
     * @return bool
     */    
    public function isValid($value, $field)
    {   
        if ($this->request->isPost()) {

            if (false == $this->captcha->result($value)->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return true;
        }
        return false;
    }
}