<?php

namespace Obullo\Validator;

use Closure;
use RuntimeException;
use Obullo\Validator\ValidatorInterface;

/**
 * Form field
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Field implements FieldInterface
{
    /**
     * Field name
     * 
     * @var string
     */
    protected $name;

    /**
     * Field label
     * 
     * @var string
     */
    protected $label;

    /**
     * Field value
     * 
     * @var mixed
     */
    protected $value;

    /**
     * Validator
     * 
     * @var object
     */
    protected $validator;

    /**
     * Dependency
     * 
     * @var object
     */
    protected $dependency;

    /**
     * Rules
     * 
     * @var array
     */
    protected $rules = array();

    /**
     * Rule parameters
     * 
     * @var array
     */
    protected $params = array();

    /**
     * Rule config
     * 
     * @var array
     */
    protected $ruleArray = array();

    /**
     * Constructor
     * 
     * @param array $row       field     data
     * @param array $ruleArray ruleArray config
     */
    public function __construct($row, $ruleArray = array())
    {
        $this->name  = $row['field'];
        $this->label = $row['label'];
        $this->value = $row['postdata'];
        $this->rules = $row['rules'];
        $this->ruleArray = $ruleArray;
    }

    /**
     * Set validator object
     * 
     * @param object $validator validator
     *
     * @return void
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Set dependency class
     * 
     * @param object $dependency container
     *
     * @return void
     */
    public function setDependency($dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * Call next rule
     * 
     * @return void
     */
    public function next()
    {
        $rule = array_shift($this->rules);

        if (! empty($rule)) {
                              
            if (strpos($rule, '(') > 0) {
                
                $matches = RuleParameter::parse($rule);
                $rule = $matches[0];
                $this->params = $matches[1];
            }
            $callbacks = $this->validator->getCallbacks(); // Is the rule has a callback?

            if (substr($rule, 0, 9) == 'callback_' && array_key_exists($rule, $callbacks)) {

                $next = Closure::bind(
                    $callbacks[$rule],
                    $this->validator,
                    get_class($this->validator)
                );
                $result = $next($this);

            } else {

                $key = strtolower($rule);
                if (! array_key_exists($key, $this->ruleArray)) {  // If rule does not exist.
                    $error = sprintf(
                        "%s rule is not defined in configuration file.",
                        ucfirst($key)
                    );
                    $this->setError($error);
                    $result = false;
                } else {
                    $Class  = $this->ruleArray[$key];
                    $next   = $this->dependency->resolve($Class);
                    $result = $next($this);
                }
            }
            if (false === $result) {
                $this->validator->dispatchErrors($this, $rule);
            }
        }
    }
    
    /**
     * Invoke next rule
     * 
     * @return boolean
     */
    public function __invoke()
    {
        return $this->next();
    }

    /**
     * Returns to field name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns to field label
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns to field value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns to field parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set rule params
     * 
     * @param array $params rule params
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Sets field value
     * 
     * @param mixed $value value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->validator->setValue($this->getName(), $value);
    }

    /**
     * Sets field error
     * 
     * @param string $value error
     *
     * @return void
     */
    public function setError($value)
    {
        $this->validator->setError($this->getName(), $value);
    }

    /**
     * Set field form message
     * 
     * @param string $message message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->validator->setMessage($message);
    }

} 
