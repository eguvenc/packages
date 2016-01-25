<?php

namespace Obullo\Validator\Rules;

use Obullo\Captcha\CaptchaInterface;
use Obullo\Validator\FieldInterface as Field;
use Psr\Http\Message\ServerRequestInterface as Request;

use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Captcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Captcha implements ImmutableContainerAwareInterface
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
    public function isValid($value, Field $field)
    {   
        if ($this->getContainer()->get('request')->isPost()) {

            if (false == $this->getContainer()->get('captcha')->result($value)->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return true;
        }
        return false;
    }
}