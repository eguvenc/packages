<?php

namespace Obullo\Validator;

use Closure;
use RuntimeException;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Form field
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Field implements FieldInterface, ValidatorAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait, ValidatorAwareTrait;

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
     * Rule object
     * 
     * @var object
     */
    protected $rule;

    /**
     * Constructor
     * 
     * @param array $row field data
     */
    public function __construct($row)
    {
        $this->name  = $row['field'];
        $this->label = $row['label'];
        $this->value = $row['postdata'];
        $this->rule  = new Rule($row['rules']);
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
     * Returns to rule object
     * 
     * @return object
     */
    public function getRule()
    {
        return $this->rule;
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
