<?php

namespace Obullo\Validator;

use Closure;
use RuntimeException;
use Obullo\Http\Controller;
use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use Interop\Container\ContainerInterface as Container;
use Obullo\Translation\TranslatorInterface as Translator;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Validator
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Validator implements ValidatorInterface
{
    protected $config;
    protected $logger;
    protected $container;
    protected $translator;
    protected $requestParams;
    protected $fieldData  = array();
    protected $errorArray = array();
    protected $formErrors = array(); 
    protected $errorPrefix = '<div>';
    protected $errorSuffix = '</div>';
    protected $errorString = '';
    protected $safeFormData = false;
    protected $validation = false;
    protected $callbackFunctions = array();
    protected $ruleArray = array();

    /**
     * Constructor
     * 
     * @param Container  $container  ContainerInterface
     * @param Config     $config     ConfigInterface
     * @param Request    $request    ServerRequestInterface
     * @param Translator $translator TranslatorInterface
     * @param Logger     $logger     LoggerInterface
     * @param array      $params     service params
     */
    public function __construct(
        Container $container, 
        Config $config, 
        Request $request, 
        Translator $translator, 
        Logger $logger,
        array $params
    ) {    
        mb_internal_encoding($config['locale']['charset']);
        
        $this->container = $container;
        $this->requestParams = $request->post();
        $this->config = $config;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->ruleArray = $params['rules'];

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
     * @param mixed  $field input fieldname
     * @param string $label input label
     * @param mixed  $rules rules string
     * 
     * @return void
     */
    public function setRules($field, $label = '', $rules = '')
    {        
        if (count($this->requestParams) == 0) {  // No reason to set rules if we have no POST data
            return;
        }
                                 // If an array was passed via the first parameter instead of indidual string
        if (is_array($field)) {  // values we cycle through it && recursively call this function.
            foreach ($field as $row) {
                if (! isset($row['field']) || ! isset($row['rules'])) { //  if we have a problem...
                    continue;
                }
                $label = ( ! isset($row['label'])) ? $this->createLabel($row['field']) : $row['label']; // If the field label wasn't passed we use the field's name
                $this->setRules($row['field'], $label, $row['rules']);  // Here we go!
            }
            return;
        }
        if (! is_string($field) || ! is_string($rules) || $field == '') { // No fields ? Nothing to do...
            return;
        }
        $label = ($label == '') ? $this->createLabel($field) : $label;  // If the field label wasn't passed we use the field name

        // Is the field name an array?  We test for the existence of a bracket "(" in
        // the field name to determine this.  If it is an array, we break it apart
        // into its components so that we can fetch the corresponding POST data later
        // 
        $this->fieldData[$field] = array(
                                            'field'    => $field, 
                                            'label'    => $label, 
                                            'rules'    => trim($rules, '|'),  // remove uneccessary pipes
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
        if (count($this->requestParams) == 0) { // Do we even have any data to process ?
            return false;
        }
        if (count($this->fieldData) == 0) {    // We're we able to set the rules correctly ?
            $this->setMessage('Unable to find validation rules');
            return true;
        }
        // Cycle through the rules for each field, match the 
        // corresponding $this->requestParams item && test for errors
        // 
        foreach ($this->fieldData as $row) {
            if (isset($row['rules'])) {
                $row['rules'] = explode('|', $row['rules']);
                $this->execute($row);
            } 
        }
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
     * Re-populate the _POST array with our finalized && processed data
     *
     * @return void
     */        
    protected function resetPostArray()
    {
        foreach ($this->fieldData as $row) {

            if (isset($row['postdata']) && ! is_null($row['postdata'])) {

                $field = $row['field'];

                if (isset($this->requestParams[$field])) {
                    $this->requestParams[$field] = $this->prepForForm($row['postdata']);
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
        return str_replace(
            array("'", '"', '<', '>'),
            array("&#39;", "&quot;", '&lt;', '&gt;'),
            stripslashes($data)
        );
    }

    /**
     * Executes the Validation routines
     * 
     * @param array $row field row 
     * 
     * @return void
     */
    protected function execute($row)
    {                   
        $field = $row['field'];
        if (strpos($field, '[') > 0) {
            $newField = str_replace('[]', '', $field);
            if (isset($this->requestParams[$newField]) && $this->requestParams[$newField] != '') {
                $row['postdata'] = $this->fieldData[$field]['postdata'] = $this->requestParams[$newField];
            } 
            
        } else {
            if (isset($this->requestParams[$field]) && $this->requestParams[$field] != '') {
                $row['postdata'] = $this->fieldData[$field]['postdata'] = $this->requestParams[$field];
            }
        }
        $field = new Field($row, $this->ruleArray);
        $field->setContainer($this->container);
        $field->setValidator($this);
        $field();
    }

    /**
     * Returns to callback functions
     * 
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbackFunctions;
    }

    /**
     * Dispatch errors
     * 
     * @param Field  $field object
     * @param string $rule  name
     * 
     * @return void
     */
    public function dispatchErrors(FieldInterface $field, $rule)
    {        
        $fieldName = $field->getName();
        $label     = $field->getLabel();
        $params    = $field->getParams();

        if (! isset($this->errorArray[$rule])) {
            $RULE = strtoupper($rule);
            $line = $this->translator['OBULLO:VALIDATOR:'.$RULE];
        } else {
            $line = $this->errorArray[$rule];
        }
        $param = (isset($params[0])) ? $params[0] : '';

        // Is the parameter we are inserting into the error message the name                                                                                  
        // of another field ?  If so we need to grab its "field label"

        if (isset($this->fieldData[$param]) && isset($this->fieldData[$param]['label'])) {        
            $param = $this->translateFieldname($this->fieldData[$param]['label']);
        }
        $message = sprintf(
            $line,
            $this->translateFieldname($label),
            $param
        );
        $this->fieldData[$fieldName]['error'] = $message;   // Save the error message
        
        if (! isset($this->errorArray[$fieldName])) {
            $this->errorArray[$fieldName] = $message;
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
     * Set form message
     * 
     * @param string $error errors
     *
     * @return void
     */
    public function setMessage($error)
    {
        $value = (string)$error;
        $value = ($this->translator->has($value)) ? $this->translator[$value] : $value;
        $this->formErrors[] = $value;
        $this->logger->debug($value);
    }

    /**
     * Get form messages
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->formErrors;
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
            $val = ($this->translator->has($val)) ? $this->translator[$val] : $val;
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
    public function setErrors(array $errors)
    {
        foreach ($errors as $k => $v) {
            $v = ($this->translator->has($v)) ? $this->translator[$v] : $v;
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
    public function callback($func, Closure $closure)
    {
        $this->callbackFunctions[$func] = $closure;
    }

    /**
     * Returns true if field name exists in validator
     * 
     * @return array
     */
    public function getFieldData()
    {
        return $this->fieldData;
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
        } elseif (isset($this->requestParams[$field])) {
            return $this->requestParams[$field];
        }
        return;
    }

    /**
     * Set filtered value to field
     * 
     * @param string $field the field name
     * @param string $value value
     * 
     * @return void
     */    
    public function setValue($field = '', $value = '')
    {
        $this->fieldData[$field]['postdata'] = $value;
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
    protected function createLabel($field)
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
     * Returns to container 
     * 
     * @return object
     */
    public function getContainer()
    {
        return $this->c;
    }
}