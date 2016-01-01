<?php

namespace Obullo\Validator;

use Closure;

/**
 * Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ValidatorInterface
{
    /**
     * Set Rules
     *
     * This function takes an array of field names && validation
     * rules as input, validates the info, && stores it
     *
     * @param mixed  $field input fieldname
     * @param string $label input label
     * @param mixed  $rules rules string
     * 
     * @return void
     */
    public function setRules($field, $label = '', $rules = '');

    /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @return bool
     */        
    public function isValid();

    /**
     * Set form message
     * 
     * @param string $error errors
     *
     * @return void
     */
    public function setMessage($error);

    /**
     * Get form messages
     * 
     * @return array
     */
    public function getMessages();

    /**
     * Set error(s) to form validator
     * 
     * @param mixed  $key key
     * @param string $val value
     * 
     * @return void
     */
    public function setError($key, $val = '');

    /**
     * Set validator errors as array
     * 
     * @param array $errors key - value
     * 
     * @return void
     */
    public function setErrors(array $errors);

    /**
     * Creates a callback function
     * 
     * @param string  $func    name
     * @param closure $closure anonymous function
     * 
     * @return void
     */ 
    public function callback($func, Closure $closure);

    /**
     * Returns to callback functions
     * 
     * @return array
     */
    public function getCallbacks();

    /**
     * Get form field data
     *
     * @return boolean
     */
    public function getFieldData();

     /**
     * Get filtered value from validator data
     *
     * Permits you to repopulate a form field with the value it was submitted
     * with, or, if that value doesn't exist, with the default
     *
     * @param string $field   the field name
     * @param string $default value
     * 
     * @return void
     */    
    public function getValue($field = '', $default = '');

    /**
     * Set filtered value to field
     * 
     * @param string $field the field name
     * @param string $value value
     * 
     * @return void
     */    
    public function setValue($field = '', $value = '');

    /**
     * Get Error Message
     *
     * Gets the error message associated with a particular field
     *
     * @return void
     */    
    public function getErrors();

    /**
     * Get error
     * 
     * @param string $field  fieldname
     * @param string $prefix error html tag start
     * @param string $suffix error html tag end
     * 
     * @return string
     */
    public function getError($field = '', $prefix = '', $suffix = '');

    /**
     * Check field has error
     * 
     * @param string $field fieldname
     * 
     * @return boolean
     */
    public function isError($field);

    /**
     * Error String
     *
     * Returns the error messages as a string, wrapped in the error delimiters
     * 
     * @param string $prefix error html tag start
     * @param string $suffix error html tag end
     * 
     * @return string
     */    
    public function getErrorString($prefix = '', $suffix = '');

    /**
     * Returns to container 
     * 
     * @return object
     */
    public function getContainer();

    /**
     * Clear object variables 
     * 
     * @return void
     */
    public function clear();
}