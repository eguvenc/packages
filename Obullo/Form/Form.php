<?php

namespace Obullo\Form;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Config\ConfigInterface as Config;
use Obullo\Container\ContainerInterface as Container;

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
     * Container
     *
     * @var object
     */
    protected $c;

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
     * @param object $config    \Obullo\Config\ConfigInterface
     * @param object $logger    \Obullo\Log\LoggerInterface
     */
    public function __construct(Container $container, Request $request, Config $config, Logger $logger)
    {
        $this->c = $container;
        $this->request = $request;

        $form = $config->load('form');
        $this->error        = $form['error'];
        $this->notification = $form['notification'];

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
        $this->messages['message'] = (string)$message;
        $this->messages['success'] = static::ERROR;
        $this->messages['code'] = 0;
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
        $this->messages['message'] = (string)$message;
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
        $this->messages['message'] = (string)$message;
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
        $this->messages['message'] = (string)$message;
        $this->messages['code'] = 3;
    }

    /**
     * Set status code
     * 
     * @param integer $code status code
     * 
     * @return void
     */
    public function code($code = 0)
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
    public function status($status = 0)
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
     * Set key for json_encode().
     * 
     * Set success, message, errors and any custom key.
     * 
     * @param string $key error key
     * @param string $val error value
     *
     * @return void
     */
    public function setKey($key, $val)
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
     * @param mixed $errors error array or validator object 
     *
     * @return void
     */
    public function setErrors($errors)
    {
        if (is_object($errors)) {
            $errors = $errors->getErrors();  // Get "Validator" object errors
        }
        if (is_array($errors) && count($errors) > 0) {
            $this->messages['success'] = 0;
        }
        $this->messages['errors'] = $errors;
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
        $this->status($status);
        $this->messages['message'] = (string)$message;
    }

    /**
     * Get notification message for valid post.
     * 
     * @param string $msg custom user message
     * 
     * @return string
     */
    public function getMessage($msg = '')
    {
        if (! empty($msg) && is_string($msg)) {
            $this->messages['message'] = (string)$msg;
        }
        if (empty($this->messages['message'])) {
            return '';
        }
        $array = $this->getValidTemplate();
        return $this->messages['message'] = str_replace(
            array('{class}','{icon}','{message}'), 
            array($array['class'], $array['icon'], $this->messages['message']),
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
    public function outputArray()
    {
        return $this->messages;
    }

    /**
     * Get all outputs of the form 
     *
     * @param array $assoc whether to return associative array
     * 
     * @return object|array|false
     */
    public function results($assoc = false)
    {
        if (isset($this->messages['results'])) {
            return ($assoc) ? $this->messages['results'] : (object)$this->messages['results'];
        }
        return false;
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
        if ($this->c->active('validator')) {
            
            return $this->c['validator']->getErrorString($prefix, $suffix);
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
        if ($this->c->active('validator')) {  // If we have validator object

            return $this->c['validator']->getError($field, $prefix, $suffix);
        }
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
        if ($this->c->active('validator') && isset($this->c['validator']->fieldData[$field])) { // If we have validator object

            return $this->c['validator']->getValue($field, $default);

        } elseif ($value = $this->request->post($field)) {

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
        $validator = $this->c['validator'];

        if (! isset($validator->fieldData[$field]) || ! isset($validator->fieldData[$field]['postdata'])) {

            if ($default === true && count($validator->fieldData) === 0) {
                return $selectedString;
            }
            if ($default === false) {
                $field = $this->request->post($field);
            }
        }

        if (isset($validator->fieldData[$field]['postdata'])) {
            $field = $validator->fieldData[$field]['postdata'];
        }

        if (is_array($field)) {
            if (! in_array($value, $field)) {
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