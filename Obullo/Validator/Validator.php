<?php

namespace Obullo\Validator;

use Closure;
use Controller;
use RuntimeException;
use Obullo\Log\LoggerInterface;
use Obullo\Config\ConfigInterface;
use Obullo\Translation\TranslatorInterface;

/**
 * Validator Class
 *
 * Modeled after Codeigniter form validation library 
 * 
 * @category  Validator
 * @package   Validator
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Validator 
{
    public $fieldData = array();
    
    protected $translator;
    protected $errorArray = array();
    protected $errorMessages = array();    
    protected $errorPrefix = '<div>';
    protected $errorSuffix = '</div>';
    protected $errorString = '';
    protected $safeFormData = false;
    protected $validation = false;
    protected $callbackFunctions = array();
    protected $filters = array();

    /**
     * Constructor
     * 
     * @param ConfigInterface     $config     \Obullo\Config\ConfigInterface
     * @param TranslatorInterface $translator \Obullo\Translation\TranslatorInterface
     * @param LoggerInterface     $logger     \Obullo\Logger\LoggerInterface
     */
    public function __construct(ConfigInterface $config, TranslatorInterface $translator, LoggerInterface $logger)
    {    
        mb_internal_encoding($config['locale']['charset']);
        
        $this->config = $config;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->translator->load('validator');
        $this->logger->debug('Validator Class Initialized');
    }

    /**
     * Clear object variables 
     * 
     * @return void
     */
    public function clear()
    {
        $this->fieldData     = array();
        $this->errorArray    = array();
        $this->errorMessages = array();
        $this->errorPrefix   = '';
        $this->errorSuffix   = '';
        $this->errorString   = '';
        $this->safeFormData  = false;
        $this->validation    = false;
    }

    /**
     * Set Rules
     *
     * This function takes an array of field names && validation
     * rules as input, validates the info, && stores it
     *
     * @param mixed  $field   input fieldname
     * @param string $label   input label
     * @param mixed  $rules   rules string
     * @param mixed  $filters php5 filters array
     * 
     * @return void
     */
    public function setRules($field, $label = '', $rules = '', $filters = array())
    {        
        if (count($_REQUEST) == 0) {  // No reason to set rules if we have no POST or GET data
            return;
        }
        if (! empty($filters)) {
            $this->filters[$field] = $filters;
        }
                                 // If an array was passed via the first parameter instead of indidual string
        if (is_array($field)) {  // values we cycle through it && recursively call this function.
            foreach ($field as $row) {
                if (! isset($row['field']) || ! isset($row['rules'])) { //  if we have a problem...
                    continue;
                }
                $label = ( ! isset($row['label'])) ? $this->createLabel($row['field']) : $row['label']; // If the field label wasn't passed we use the field's name
                $this->setRules($row['field'], $label, trim($row['rules'], '|'));  // Here we go!
            }
            return;
        }
        if (! is_string($field) ||  ! is_string($rules) || $field == '') { // No fields? Nothing to do...
            return;
        }
        $label = ($label == '') ? $this->createLabel($field) : $label;  // If the field label wasn't passed we use the field name
        // Is the field name an array?  We test for the existence of a bracket "(" in
        // the field name to determine this.  If it is an array, we break it apart
        // into its components so that we can fetch the corresponding POST data later     

        if (strpos($field, '(') !== false && preg_match_all('/\((.*?)\)/', $field, $matches)) {
            $x = explode('(', $field);
            // Note: Due to a bug in current() that affects some versions
            // of PHP we can not pass function call directly into it.
            $indexes[] = current($x);
            for ($i = 0; $i < count($matches['0']); $i++) {
                if ($matches['1'][$i] != '') {
                    $indexes[] = $matches['1'][$i];
                }
            }
            $isArray = true;
        } else {
            $indexes = array();
            $isArray = false;        
        }
        $this->fieldData[$field] = array(
                                            'field'    => $field, 
                                            'label'    => $label, 
                                            'rules'    => trim($rules, '|'),  // remove uneccessary pipes
                                            'isArray' => $isArray,
                                            'keys'     => $indexes,
                                            'postdata' => null,
                                            'error'    => '',
                                        );
    }

    /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @return bool
     */        
    public function isValid()
    {
        if (count($_REQUEST) == 0) { // Do we even have any data to process ?
            return false;
        }                                     // Does the fieldDataarray containing the validation rules exist ?     
        if (count($this->fieldData) == 0) {   // If not, we look to see if they were assigned via a config file              
                                              // No validation rules ?  We're done...        
            if (sizeof($this->fieldData) == 0) {    // We're we able to set the rules correctly ?
                $this->errorMessages['message'] = 'Unable to find validation rules';
                $this->logger->debug($this->errorMessages['message']);
                return true;
            }
        }
        // Cycle through the rules for each field, match the 
        // corresponding $_REQUEST item && test for errors
        foreach ($this->fieldData as $field => $row) {  // Fetch the data from the corresponding $_REQUEST array && cache it in the fieldDataarray.
                                                        // Depending on whether the field name is an array or a string will determine where we get it from.
            if (isset($row['isArray']) && $row['isArray'] == true) {
                $this->fieldData[$field]['postdata'] = $this->reduceArray($_REQUEST, $row['keys']);
            } else {
                if (isset($_REQUEST[$field]) && $_REQUEST[$field] != '') {
                    $this->fieldData[$field]['postdata'] = $_REQUEST[$field];
                }
            }
            if (isset($row['rules'])) {  // If we have no rule don't run validation ( e.g. we can set errors using setError() function without validation set rules.)
                $this->execute($row, explode('|', $row['rules']), $this->fieldData[$field]['postdata']);       
            } 
        }

        // $this->validateInputFilters();  // Execute the filters.

        $totalErrors = sizeof($this->errorArray);         // Did we end up with any errors?
        if ($totalErrors > 0) {
            $this->safeFormData = true;
        }
        $this->resetPostArray();    // Now we need to re-set the POST data with the new, processed data
        if ($totalErrors == 0) {    // No errors, validation passes !
            $this->validation = true;
            return true;
        }
        return false;         // Validation fails
    }

    /**
     * Traverse a multidimensional $_REQUEST array index until the data is found
     *
     * @param array   $array data
     * @param array   $keys  keys
     * @param integer $i     iterator
     * 
     * @return mixed
     */        
    protected function reduceArray($array, $keys, $i = 0)
    {
        if (is_array($array)) {
            if (isset($keys[$i])) {
                if (isset($array[$keys[$i]])) {
                    $array = $this->reduceArray($array[$keys[$i]], $keys, ($i+1));
                } else {
                    return null;
                }
            } else {
                return $array;
            }
        }
        return $array;
    }

    /**
     * Re-populate the _POST array with our finalized && processed data
     *
     * @return void
     */        
    protected function resetPostArray()
    {
        foreach ($this->fieldData as $row) {
            if (isset($row['postdata']) && ! is_null($row['postdata'])) {
                if (isset($row['isArray']) && $row['isArray'] == false) {
                    if (isset($_REQUEST[$row['field']])) {
                        $_REQUEST[$row['field']] = $this->prepForForm($row['postdata']);
                    }
                } else {
                    $post_ref =& $_REQUEST;   // start with a reference
                    if (isset($row['keys'])) {
                        if (count($row['keys']) == 1) { // before we assign values, make a reference to the right POST key
                            $post_ref =& $post_ref[current($row['keys'])];
                        } else {
                            foreach ($row['keys'] as $val) {
                                $post_ref =& $post_ref[$val];
                            }
                        }
                    }
                    if (is_array($row['postdata'])) {
                        $array = array();
                        foreach ($row['postdata'] as $k => $v) {
                            $array[$k] = $this->prepForForm($v);
                        }
                        $post_ref = $array;
                    } else {
                        $post_ref = $this->prepForForm($row['postdata']);
                    }
                }
            }
        }
    }
    
    /**
     * Prep data for form
     *
     * This function allows HTML to be safely shown in a form.
     * Special characters are converted.
     *
     * @param array $data prep data
     * 
     * @return string
     */
    public function prepForForm($data = '')
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = $this->prepForForm($val);
            }
            return $data;
        }
        if ($this->safeFormData == false || $data === '') {
            return $data;
        }
        return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
    }

    /**
     * Executes the Validation routines
     * 
     * @param array   $row      field row    
     * @param string  $rules    rules
     * @param array   $postdata post data
     * @param integer $cycles   cycles
     * 
     * @return void
     */
    protected function execute($row, $rules, $postdata = null, $cycles = 0)
    {                   
        if (is_array($postdata)) {  // If the $_REQUEST data is an array we will run a recursive call
            foreach ($postdata as $val) {
                $this->execute($row, $rules, $val, $cycles);
                $cycles++;
            }
            return;
        }
        $callback = false;         // If the field is blank, but NOT required, no further tests are necessary
        if (! in_array('required', $rules) && is_null($postdata)) {
            if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match)) {  // Before we bail out, does the rule contain a callback?
                $callback = true;
                $rules = (array('1' => $match[1]));
            } else {
                return;
            }
        }
        if (is_null($postdata) && $callback == false) {    // Isset Test. Typically this rule will only apply to checkboxes.
            if (in_array('required', $rules)) {
                if (! isset($this->errorMessages['required'])) {
                    $line = $this->translator['OBULLO:VALIDATOR:REQUIRED'];
                    if ($line == false) {
                        $line = 'The field was not set';
                    }
                } else {
                    $line = $this->errorMessages['required'];
                }
                $message = sprintf($line, $this->translateFieldname($row['label'])); // Build the error message
                $this->fieldData[$row['field']]['error'] = $message;                 // Save the error message
                if (! isset($this->errorArray[$row['field']])) {
                    $this->errorArray[$row['field']] = $message;
                }
            }
            return;
        }
        foreach ($rules as $rule) {   // Cycle through each rule && run it
            $inArray = false;         // We set the $postdata variable with the current data in our master array so that
                                      // each cycle of the loop is dealing with the processed data from the last cycle
            if ($row['isArray'] == true && is_array($this->fieldData[$row['field']]['postdata'])) {
                if (! isset($this->fieldData[$row['field']]['postdata'][$cycles])) {   // We shouldn't need this safety,
                                                                                        // but just in case there isn't an array index
                    continue;                                                           // associated with this cycle we'll bail out
                }
                $postdata = $this->fieldData[$row['field']]['postdata'][$cycles];
                $inArray = true;
            } else {
                $postdata = $this->fieldData[$row['field']]['postdata'];
            }
            $callback = false;
            if (substr($rule, 0, 9) == 'callback_') {  // Is the rule a callback? 
                $callback = true;
            }
            $param = false;                                             // Strip the parameter (if exists) from the rule
            if (preg_match_all("/(.*?)\((.*?)\)/", $rule, $matches)) {  // Rules can contain parameters: minLen(5),                    
                $rule    = $matches[1][0];
                $param   = $matches[2][0];
            }
            if ($callback === true) {    // Call the function that corresponds to the rule
                if (! array_key_exists($rule, $this->callbackFunctions)) {  // Check method exists in callback object.
                    continue;
                }
                $closure = Closure::bind($this->callbackFunctions[$rule], $this, get_class());
                $result  = $closure($postdata, $param);  // Run the function and grab the result

                if ($inArray == true) { // Re-assign the result to the master data array
                    $this->fieldData[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                } else {
                    $this->fieldData[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                }
                if (! in_array('required', $rules, true) && $result !== false) {
                    continue;
                }
            } else {

                $ruleClass = ucfirst($rule);
                $className = 'Obullo\\Validator\\'.ucfirst($rule);

                if (! file_exists(OBULLO .'Validator/' .$ruleClass. '.php')) {  // If our own wrapper function doesn't exist we see if a native PHP function does. 
                                                                                 // Users can use any native PHP function call that has one param.
                    if (function_exists($rule)) { // Native php func.
                        $result = $rule($postdata);
                        if ($inArray == true) {
                            $this->fieldData[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                        } else {
                            $this->fieldData[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                        }
                    } else {
                        $this->errorMessages['message'] = 'The '.ucfirst($rule).' is not a valid rule, if you have new validation function do pull request on the github.';
                        $this->logger->error($this->errorMessages['message']);
                    } 
                    continue;
                }
                if (! class_exists($className)) {
                    include OBULLO .'Validator/' .$ruleClass. '.php';
                }
                $result = call_user_func_array(array(new $className($this->config, $this->logger), 'isValid'), array($postdata, $param));
                if ($inArray == true) {
                    $this->fieldData[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                } else {
                    $this->fieldData[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                }
            }
            if ($result === false) { // Did the rule test negatively?  If so, grab the error.
                
                if (! isset($this->errorMessages[$rule])) {
                    $RULE = strtoupper($rule);
                    $line = $this->translator['OBULLO:VALIDATOR:'.$RULE];
                    if ($this->translator[$rule] == false) {
                        $line = 'Error message is not set correctly or unable to translation access an error message.';
                        $this->logger->error($line);
                    }
                } else {
                    $line = $this->errorMessages[$rule];
                }
                if (isset($this->fieldData[$param]) && isset($this->fieldData[$param]['label'])) { 
                    // Is the parameter we are inserting into the error message the name                                                                                  
                    // of another field?  If so we need to grab its "field label"
                    $param = $this->translateFieldname($this->fieldData[$param]['label']);
                }
                $message = sprintf($line, $this->translateFieldname($row['label']), $param); // Build the error message
                $this->fieldData[$row['field']]['error'] = $message;   // Save the error message
                
                if (! isset($this->errorArray[$row['field']])) {
                    $this->errorArray[$row['field']] = $message;
                }
                return;
            }
        }
    }

    /**
     * Translate a field name
     *
     * @param string $fieldname the field name
     * 
     * @return string
     */    
    protected function translateFieldname($fieldname)
    {
        if (substr($fieldname, 0, 10) == 'translate:') { // Do we need to translate the field name? 
            $line = substr($fieldname, 10);  // Grab the variable
            if ($this->translator[$line]) {  // Were we able to translate the field name? If not we use $line.
                return $this->translator[$line];
            }
        }
        return $fieldname;
    }

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
    public function setMessage($key, $val = '')
    {
        if (! is_array($key)) {
            if ($val == '' && count($this->callbackFunctions) > 0) {
                $callbackKeys = array_keys($this->callbackFunctions);
                $val = $key;
                $key = end($callbackKeys);
            }
            $key = array($key => $val);
        }
        $this->errorMessages = array_merge($this->errorMessages, $key);
    }

    /**
     * Set error(s) to form validator
     * 
     * @param mixed  $key key
     * @param string $val value
     * 
     * @return void
     */
    public function setError($key, $val = '')
    {
        if (is_array($key)) {
            $this->setErrors($key);
        } else {
            $this->fieldData[$key]['error'] = $val;
            $this->errorArray[$key] = $val;
        }
    }

    /**
     * Set validator errors as array
     * 
     * @param array $errors key - value
     * 
     * @return void
     */
    public function setErrors($errors)
    {
        foreach ($errors as $k => $v) {
            $this->fieldData[$k]['error'] = $v;
            $this->errorArray[$k] = $v;
        }
    }

    /**
     * Create a callback function
     * for validator
     * 
     * @param string  $func    name
     * @param closure $closure anonymous function
     * 
     * @return void
     */
    public function func($func, $closure)
    {
        $this->callbackFunctions[$func] = $closure;
    }

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
    public function getValue($field = '', $default = '')
    {
        if (! isset($this->fieldData[$field])) {
            return $default;
        }
        if (isset($this->fieldData[$field]['postdata'])) { 
            return $this->fieldData[$field]['postdata'];
        } elseif (isset($_REQUEST[$field])) {
            return $_REQUEST[$field];
        }
        return;
    }

    /**
     * Set filtered value from validator data
     *
     * Permits you to repopulate a form field with the value it was submitted
     * with, or, if that value doesn't exist, with the default
     *
     * @param string $field   the field name
     * @param string $default value
     * 
     * @return void
     */    
    public function setValue($field = '', $default = '')
    {
        return $this->getValue($field, $default);
    }

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
    public function value($field = '', $default = '')
    {
        return $this->getValue($field, $default);
    }

    /**
     * Get Error Message
     *
     * Gets the error message associated with a particular field
     *
     * @return void
     */    
    public function getErrors()
    {    
        return $this->errorArray;
    }

    /**
     * Get error
     * 
     * @param string $field  fieldname
     * @param string $prefix error html tag start
     * @param string $suffix error html tag end
     * 
     * @return string
     */
    public function getError($field = '', $prefix = '', $suffix = '')
    {
        if ($prefix == '' && $suffix == '') {
            $prefix = $this->errorPrefix;
            $suffix = $this->errorSuffix;
        }
        if ($this->isError($field)) {
            return $prefix.$this->errorArray[$field].$suffix;
        }
        return '';
    }

    /**
     * Check field has error
     * 
     * @param string $field fieldname
     * 
     * @return boolean
     */
    public function isError($field)
    {
        if (! isset($this->fieldData[$field]['error']) || $this->fieldData[$field]['error'] == '') {
            return false;
        }
        return true;
    }

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
    public function getErrorString($prefix = '', $suffix = '')
    {
        if (sizeof($this->errorArray) === 0) { // No errrors, validation passes !
            return '';
        }
        $str = '';        
        foreach ($this->errorArray as $val) { // Generate the error string
            if ($val != '') {
                if ($prefix == '' && $suffix == '') {
                    $str .= $this->errorPrefix.$val.$this->errorSuffix;
                } else {
                    $str .= $prefix.$val.$suffix."\n";
                }
            }
        }
        return $str;
    }

    /**
     * Set The Error Delimiter
     *
     * Permits a prefix/suffix to be added to each error message
     *
     * @param string $prefix html
     * @param string $suffix html
     * 
     * @return void
     */    
    public function setErrorDelimiters($prefix = '<p>', $suffix = '</p>')
    {
        $this->errorPrefix = $prefix;
        $this->errorSuffix = $suffix;
    }

    /**
     * Assign all controller objects into validator class
     * to callback closure $this->object support.
     *
     * @param string $key key
     * 
     * @return void
     */
    public function __get($key)
    {
        return Controller::$instance->{$key};
    }

    /**
     * Create label automatically.
     * 
     * @param string $field field name
     * 
     * @return string label
     */
    public function createLabel($field)
    {
        $label = ucfirst($field);
        if (strpos($field, '_') > 0) {
            $words = explode('_', strtolower($field));
            $ucwords = array_map('ucwords', $words);
            $label = implode(' ', $ucwords);
        }
        return $label;
    }

    /**
     * Executes callbackFunction() method in given object.
     * 
     * @param object $object 
     *
     * @return void
     */
    public function bind($object)
    {
        $method = "callbackFunction";
        if (! method_exists($object, $method)) {
            throw new RuntimeException(
                sprintf(
                    "The object %s does not contain %s method.",
                    get_class($object),
                    $method
                )
            );
        }
        $object->$method();
    }

}