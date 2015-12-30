<?php

namespace Obullo\Validator;

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
     * Set Error Message
     *
     * Lets users set their own error messages on the fly.  Note:  The key
     * name has to match the function name that it corresponds to.
     *
     * @param string $key key
     * @param string $val val
     * 
     * @return string
     */
    public function setMessage($key, $val = '');

    /**
     * Set warning errors
     * 
     * @param string $error errors
     *
     * @return void
     */
    public function setFormMessage($error);

    /**
     * Get warning messages
     * 
     * @return array
     */
    public function getFormMessages();

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
     * Create a callback function
     * for validator
     * 
     * @param string  $func    name
     * @param closure $closure anonymous function
     * 
     * @return void
     */
    public function func($func, $closure);

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