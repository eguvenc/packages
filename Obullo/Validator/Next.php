<?php

namespace Obullo\Validator;

use Closure;
use League\Container\ImmutableContainerAwareInterface;
use League\Container\ContainerAwareInterface;

/**
 * Parse rule parameters
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Next implements ImmutableValidatorAwareInterface
{
    use ImmutableValidatorAwareTrait;

    /**
     * Call next rule
     * 
     * @param Field $field field
     * 
     * @return object|boolean
     */
    public function __invoke(FieldInterface $field)
    {
        $rule = $field->getNextRule();

        if (! empty($rule) && is_string($rule)) {
                              
            $rule = strtolower($rule);

            if (strpos($rule, '(') > 0) {

                $matches = RuleParameter::parse($rule);
                $rule = $matches[0];
                $field->setParams($matches[1]);
            }
            if (substr($rule, 0, 9) == 'callback_') {

                $callbacks = $this->getValidator()->getCallbacks(); // Is the rule has a callback?

                if (! array_key_exists($rule, $callbacks)) {

                    $error = sprintf(
                        "%s rule is not defined as callback method.",
                        $rule
                    );
                    $field->setError($error);
                }
                $result = $this->callNextClosure($field, $rule, $callbacks);

            } else {

                $result = $this->callNextClass($field, $rule);
            }

            if (false === $result) {
                $this->getValidator()->dispatchErrors($field, $rule);
            }
            return $result;
        }
        return true;
    }

    /**
     * Run next class
     * 
     * @param object $field field
     * @param string $rule  rule
     * 
     * @return mixed
     */
    protected function callNextClass(FieldInterface $field, $rule)
    {
        $Rules = $this->getValidator()->getRules();

        if (! array_key_exists($rule, $Rules)) {  // If rule does not exist.

            $error = sprintf(
                "%s rule is not defined in configuration file.",
                $rule
            );
            $field->setError($error);
            return false;

        } else {

            $Class = "\\".trim($Rules[$rule], '\\');
            $nextRule = new $Class;

            if ($nextRule instanceof ImmutableContainerAwareInterface || $nextRule instanceof ContainerAwareInterface) {
                $nextRule->setContainer($this->getValidator()->getContainer());
            }
            return $nextRule($field, $this);
        }
    }

    /**
     * Run next callback
     * 
     * @param object $field     field
     * @param string $rule      rule
     * @param array  $callbacks callback stack
     * 
     * @return mixed
     */
    protected function callNextClosure(FieldInterface $field, $rule, $callbacks)
    {
        $validator = $this->getValidator();
        
        $next = Closure::bind(
            $callbacks[$rule],
            $validator,
            get_class($validator)
        );
        return $next($field, $this);
    }

}