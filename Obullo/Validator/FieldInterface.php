<?php

namespace Obullo\Validator;

use RuntimeException;
use Obullo\Validator\ValidatorInterface as Validator;

/**
 * Form field interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface FieldInterface
{
    /**
     * Set validator object
     * 
     * @param object $validator validator
     *
     * @return void
     */
    public function setValidator(Validator $validator);

    /**
     * Call next rule
     * 
     * @return void
     */
    public function next();

    /**
     * Invoke next rule
     * 
     * @return boolean
     */
    public function __invoke();

    /**
     * Sets field value
     * 
     * @param mixed $value value
     *
     * @return void
     */
    public function setValue($value);

    /**
     * Returns to field value
     * 
     * @return mixed
     */
    public function getValue();

    /**
     * Returns to field name
     * 
     * @return string
     */
    public function getName();

    /**
     * Returns to field label
     * 
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns to field parameters
     * 
     * @return array
     */
    public function getParams();

    /**
     * Sets field error
     * 
     * @param string $value error
     *
     * @return void
     */
    public function setError($value);
    
    /**
     * Set field form message
     * 
     * @param string $message message
     *
     * @return void
     */
    public function setFormMessage($message);

}