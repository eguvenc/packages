<?php

namespace Obullo\Form;

use Psr\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;
use Obullo\Validator\ValidatorInterface as Validator;

use Psr\Http\Message\RequestInterface as Request;

/**
 * Form Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Form
{
    /**
     * Form status values
     */
    const ERROR = 0;
    const SUCCESS = 1;
    /**
     * Form code values
     */
    const CODE_ERROR = 0;
    const CODE_SUCCESS = 1;
    const CODE_WARNING = 2;
    const CODE_INFO = 3;

    /**
     * Form element
     * 
     * @var object
     */
    protected $element;

    /**
     * Container
     *
     * @var object
     */
    protected $container;

    /**
     * Notification config
     * 
     * @var array
     */
    protected $notification = array();

    /**
     * Error config
     * 
     * @var array
     */
    protected $error = array();

    /**
     * Store form notification and errors
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Request
     * 
     * @var object
     */
    protected $request;

    /**
     * Constructor
     *
     * @param object $container \Obullo\Container\ContainerInterface
     * @param object $request   \Obullo\
     * @param object $logger    \Obullo\Log\LoggerInterface
     * @param array  $params    service parameters
     */
    public function __construct(Container $container, Request $request, Logger $logger, array $params)
    {
        $this->request = $request;
        $this->container = $container;

        $this->error        = $params['validation']['error'];
        $this->notification = $params['notification'];

        $this->messages['success'] = static::ERROR;
        $this->messages['code'] = 0;

        $this->logger = $logger;
        $this->logger->debug('Form Class Initialized');
    }

    /**
     * Set errors message and set status to "0".
     * 
     * @param string $message success message
     * 
     * @return void
     */
    public function error($message)
    {
        $this->messages['messages'][] = (string)$message;
        $this->messages['success'] = static::ERROR;
        $this->messages['code'] = 0;
        $this->setErrors();
    }

    /**
     * Set success message and set status to "1".
     * 
     * @param string $message success message
     * 
     * @return void
     */
    public function success($message)
    {
        $this->messages['messages'][] = (string)$message;
        $this->messages['success'] = static::SUCCESS;
        $this->messages['code'] = 1;
    }

    /**
     * Set warning message
     * 
     * @param string $message success message
     * 
     * @return void
     */
    public function warning($message)
    {
        $this->messages['messages'][] = (string)$message;
        $this->messages['code'] = 2;
    }

    /**
     * Set info message
     * 
     * @param string $message success message
     * 
     * @return void
     */
    public function info($message)
    {
        $this->messages['messages'][] = (string)$message;
        $this->messages['code'] = 3;
    }

    /**
     * Set status code
     * 
     * @param integer $code status code
     * 
     * @return void
     */
    public function setCode($code = 0)
    {
        $this->messages['code'] = (int)$code;
    }

    /**
     * Returns to status code
     * 
     * @return integer
     */
    public function getCode()
    {
        return $this->messages['code'];
    }

    /**
     * Set form status
     * 
     * @param integer $status 1 or 0
     * 
     * @return void
     */
    public function setStatus($status = 0)
    {
        $this->messages['success'] = (int)$status;
    }

    /**
     * Returns to form status
     * 
     * @return void
     */
    public function getStatus()
    {
        return $this->messages['success'];
    }

    /**
     * Set custom items to message array
     * 
     * Set success, message, errors and any custom key.
     * 
     * @param string $key error key
     * @param string $val error value
     *
     * @return void
     */
    public function setItem($key, $val)
    {
        $this->messages[$key] = $val;
    }

    /**
     * Set api results
     * 
     * @param array $results api result messages
     *
     * @return void
     */
    public function setResults($results)
    {
        $this->messages['results'] = $results;
    }

    /**
     * Set validator errors array to form e.g. : array('field' => 'error', 'field2' => 'error' )
     * 
     * @param mixed $errors array or validator object 
     *
     * @return void
     */
    public function setErrors($errors = null)
    {
        if ($errors == null) {
            $errors = $this->container->get('validator');
        }
        if (is_object($errors) && $errors instanceof Validator) {

            $errorArray = $errors->getErrors();  // Get validator errors
            $formMessages = $errors->getMessages();

            if (count($formMessages) > 0) {
                $this->messages['success'] = 0;
                foreach ($formMessages as $value) {
                    $this->messages['messages'][] = $value; // Add form messages
                }
            }
        }
        if (is_array($errorArray) && count($errorArray) > 0) {
            $this->messages['success'] = 0;
        }
        $this->messages['errors'] = $errorArray;
    }

    /**
     * Set notification message and status
     * 
     * @param string $message form message
     * @param int    $status  status default NOTICE_ERROR
     * 
     * @return void
     */
    public function setMessage($message, $status = 0)
    {
        $this->setStatus($status);
        $this->messages['messages'][] = (string)$message;
    }

    /**
     * Get notification message for valid post.
     * 
     * @param string $msg custom user message
     * 
     * @return string
     */
    public function getMessageString($msg = '')
    {
        if (! empty($msg) && is_string($msg)) {
            $this->messages['messages'][] = (string)$msg;
        }
        if (empty($this->messages['messages'])) {
            return '';
        }
        $messageStr = '';
        foreach ($this->messages['messages'] as $message) {
            $messageStr.= $this->addTemplate($message);
        }
        return $messageStr; 
    }

    /**
     * Returns to message array
     * 
     * @return array
     */
    public function getMessageArray()
    {
        $messages = array();
        foreach ($this->messages['messages'] as $message) {
            $messages[] = $this->addTemplate($message);
        }
        return $messages; 
    }

    /**
     * Add message template
     * 
     * @param string $message message
     *
     * @return string
     */
    public function addTemplate($message)
    {
        $array = $this->getValidTemplate();
        return str_replace(
            array('{class}','{icon}','{message}'), 
            array($array['class'], $array['icon'], $message),
            $this->notification['message']
        );
    }

    /**
     * Get current status template from configuration.
     * 
     * @return string error status
     */
    public function getValidTemplate()
    {
        $code = $this->messages['code'];
        $errors = [
            static::CODE_ERROR => 'error',
            static::CODE_SUCCESS => 'success',
            static::CODE_WARNING => 'warning',
            static::CODE_INFO => 'info',
        ];
        if (isset($errors[$code]) && isset($this->notification[$errors[$code]])) {
            return $this->notification[$errors[$code]];
        }
        return ($this->getStatus()) ? $this->notification['success'] : $this->notification['error'];
    }

    /**
     * Get all outputs of the form 
     * 
     * @return array
     */
    public function getOutputArray()
    {
        return $this->messages;
    }

    /**
     * Get all outputs of the form 
     * 
     * @return object|array|false
     */
    public function getResultArray()
    {
        if (isset($this->messages['results'])) {
            return $this->messages['results'];
        }
        return false;
    }

    /**
     * Creates form element object
     * 
     * @return object
     */
    public function getElement()
    {
        if ($this->element != null) {
            return $this->element;
        }
        return $this->element = new Element(
            $this->container,
            $this->container->get('request'),
            $this->container->get('config'),
            $this->container->get('logger')
        );
    }

    /**
     * Get validation error from validator object
     * 
     * @param string $prefix error prefix
     * @param string $suffix error suffix
     * 
     * @return string
     */
    public function getValidationErrors($prefix = '', $suffix = '')
    {
        if ($this->container->hasShared('validator')) {
            return $this->container->get('validator')->getErrorString($prefix, $suffix);
        }
    }

    /**
     * Get error
     * 
     * @param string $field  fieldname
     * @param string $prefix error html tag start
     * @param string $suffix error html tag end
     * 
     * @return mixed string or null
     */
    public function getError($field, $prefix = '', $suffix = '')
    {
        if ($this->container->hasShared('validator') && $this->isError($field)) {  // If we have validator object
            return $this->container->get('validator')->getError($field, $prefix, $suffix);
        }
    }

    /**
     * Check field has error
     * 
     * @param string $field name
     * 
     * @return boolean
     */
    public function isError($field)
    {
        return $this->container->get('validator')->isError($field);
    }

    /**
     * Get error css class attribute
     * 
     * @param string $field fieldname
     * 
     * @return mixed string or null
     */
    public function getErrorClass($field)
    {
        if ($this->getError($field)) {
            return $this->error['class'];
        }
    }

    /**
     * Get error with css label tag
     * 
     * @param string $field fieldname
     * 
     * @return mixed string or null
     */
    public function getErrorLabel($field)
    {
        if ($error = $this->getError($field)) {
            return sprintf($this->error['label'], $field, $error);
        }
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
     * @return mixed string or null
     */    
    public function getValue($field = '', $default = '')
    {
        if ($this->container->hasShared('validator')) { // If we have load before validator object

            $fieldData = $this->container->get('validator')->getFieldData();

            if (isset($fieldData[$field])) {
                return $this->container->get('validator')->getValue($field, $default);
            }
        } elseif ($value = $this->request->all($field)) {

            return $value;
        }
        return $default;
    }

    /**
     * Alias of getValue
     * 
     * @param string $field   the field name
     * @param string $default value
     *
     * @return mixed string or null
     */
    public function setValue($field = '', $default = '')
    {
        return $this->getValue($field, $default);
    }

    /**
     * Set Select
     *
     * Enables pull-down lists to be set to the value the user
     * selected in the event of an error
     * 
     * @param string  $field          fieldname
     * @param string  $value          value
     * @param boolean $default        default value
     * @param string  $selectedString selected string text
     *
     * @return void
     */
    public function setSelect($field = '', $value = '', $default = false, $selectedString = ' selected="selected"')
    {
        $fieldData = $this->container->get('validator')->getFieldData();

        if (! isset($fieldData[$field]) || ! isset($fieldData[$field]['postdata'])) {

            if ($default === true && count($fieldData) === 0) {
                return $selectedString;
            }
            if ($default === false) {
                $field = $this->request->all($field);
            }
        }
        if (isset($fieldData[$field]['postdata'])) {
            $field = $fieldData[$field]['postdata'];
        }
        if (is_array($field)) {
            if (! $this->inArray($value, $field)) {
                return '';
            }
        } else {
            if (($field == '' || $value == '') || ($field != $value)) {
                return '';
            }
        }
        return $selectedString;  // ' selected="selected"'
    }

    /**
     * Search multi dimensional array
     * 
     * @param mixed $search value
     * @param array $array  array
     * 
     * @return boolean
     */
    protected function inArray($search, $array = array())
    {
        $inArray = false;
        foreach ($array as $val) {
            if (is_array($val)) {
                return $this->inArray($search, $val);
            } else {
                if ($search == $val) {
                    $inArray = true;
                }
            }
        }
        return $inArray;
    }

    /**
     * Selet Checkbox Item
     *
     * Enables checkboxes to be set to the value the user
     * selected in the event of an error
     *
     * @param string  $field   fieldname
     * @param string  $value   value
     * @param boolean $default default value
     *
     * @return void
     */    
    public function setCheckbox($field = '', $value = '', $default = false)
    {
        return $this->setSelect($field, $value, $default, ' checked="checked"');
    }

    /**
     * Select Radio Item
     *
     * Enables radio buttons to be set to the value the user
     * selected in the event of an error
     *
     * @param string  $field   fieldname
     * @param string  $value   value
     * @param boolean $default default value
     *
     * @return void
     */    
    public function setRadio($field = '', $value = '', $default = false)
    {
        return $this->setSelect($field, $value, $default, ' checked="checked"');
    }

}
