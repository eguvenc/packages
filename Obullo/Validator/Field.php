<?php

namespace Obullo\Validator;

use Closure;
use RuntimeException;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Form field
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Field implements FieldInterface, ImmutableValidatorAwareInterface, ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait, ImmutableValidatorAwareTrait;

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
     * Constructor
     * 
     * @param array $row field     data
     */
    public function __construct($row)
    {
        $this->name  = $row['field'];
        $this->label = $row['label'];
        $this->value = $row['postdata'];
        $this->rules = $row['rules'];
    }

    /**
     * Call next rule
     * 
     * @return void
     */
    public function getNextRule()
    {
        return array_shift($this->rules);
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
     * @return array|boolean
     */
    public function getParams()
    {
        return isset($this->params[0]) ? $this->params : false;
    }

    /**
     * Set field rule params e.g. min(5)|max(500)
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
        $this->getValidator()->setValue($this->getName(), $value);
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
        $this->getValidator()->setError($this->getName(), $value);
    }

    /**
     * Returns to0 field error
     *
     * @return void
     */
    public function getError()
    {
        return $this->getValidator()->getError($this->getName());
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
        $this->getValidator()->setMessage($message);
    }

} 
