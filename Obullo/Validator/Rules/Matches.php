<?php

namespace Obullo\Validator\Rules;

use Obullo\Validator\FieldInterface as Field;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Matches
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Matches implements ImmutableContainerAwareInterface
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
        $matchField = '';
        if ($params = $field->getParams()) {
            $matchField = $params[0];
        }
        $container  = $this->getContainer();

        if ($matchValue = $container->get('request')->post($matchField)) {

            if ($field->getValue() !== $matchValue) {
                return false;
            }
        }
        return $next($field);
    }
}