<?php

namespace Obullo\Validator\Rules;

use Obullo\Captcha\CaptchaInterface;
use Obullo\Validator\FieldInterface as Field;
use Psr\Http\Message\ServerRequestInterface as Request;

use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * ReCaptcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptcha implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

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
        if ($this->getContainer()->get('request')->isPost()) {

            $value = $this->getContainer()
                ->get('request')
                ->post('g-recaptcha-response');

            if (false == $this->getContainer()->get('recaptcha')->result($value)->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return true;
        }
        return false;
    }
}