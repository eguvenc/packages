<?php

namespace Obullo\Validator\Rules;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Validator\FieldInterface as Field;

/**
 * Matches
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Matches implements ContainerAwareInterface
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
        $matchField = $field->getRule()->getParam(0, '');
        $container  = $this->getContainer();

        if ($matchValue = $container->get('request')->post($matchField)) {

            if ($field->getValue() !== $matchValue) {
                return false;
            }
        }
        return $next($field);
    }
}