<?php

namespace Obullo\Validator\Rules;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Validator\FieldInterface as Field;

/**
 * Captcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Captcha implements ContainerAwareInterface
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

            if (false == $container->get('captcha')->result($field->getValue())->isValid()) {
                $field->setError('OBULLO:VALIDATOR:CAPTCHA:VALIDATION');
                return false;
            }
            return $next($field);
        }
        return false;
    }

}