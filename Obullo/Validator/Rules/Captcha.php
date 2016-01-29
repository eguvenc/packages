<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;
use Obullo\Captcha\CaptchaInterface;
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
     * Check captcha
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        $field     = $this->getField();
        $container = $this->getContainer();

        if ($container->get('request')->isPost()) {

            if (false == $container->get('captcha')->result($value)->isValid()) {
                
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return true;
        }
        return false;
    }
}