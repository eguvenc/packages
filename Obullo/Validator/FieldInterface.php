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
     * Returns to rule
     * 
     * @return object
     */
    public function getRule();

    /**
     * Returns to field label
     * 
     * @return string
     */
    public function getLabel();

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
    public function setMessage($message);

}