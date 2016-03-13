<?php

namespace Obullo\Validator\Rules;

use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Validator\FieldInterface as Field;

/**
 * ReCaptcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptcha implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Call next
     * 
     * @param Field    $field object
     * @param Callable $next  object
     * 
     * @return object
     */
    public function __invoke(Field $field, Callable $next)
    {
        $container = $this->getContainer();

        if ($container->get('request')->isPost()) {

            $value = $container
                ->get('request')
                ->post('g-recaptcha-response');

            if (false == $this->getContainer()->get('recaptcha')->result($value)->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return $next($field);
        }
        return false;
    }
}