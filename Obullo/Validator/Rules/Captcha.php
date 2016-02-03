<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;
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