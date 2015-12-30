<?php

namespace Obullo\Validator;

/**
 * Date 
 * 
 * Borrowed from Zend
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Date
{
    /**
     * Value
     * 
     * @var string
     */
    public $value = '';

    /**
     * Format
     * 
     * @var string
     */
    public $format = '';
     
    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(ValidatorInterface $validator, $field, $params = array())
    {
        $validator = $field = null;
        $this->format = isset($params[0]) ? $params[0] : 'Y-m-d';
    }

    /**
     * Date check
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        $this->value = $value;
        return (! $this->convertToDateTime($this->value)) ? false : true ;
    }

     /**
     * Attempts to convert an int, string, or array to a DateTime object
     *
     * @param string|int|array $value value
     * 
     * @return bool|DateTime
     */
    protected function convertToDateTime($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }
        $type = gettype($value);
        if (!in_array($type, array('string', 'integer'))) {
            return false;
        }
        $convertMethod = 'convert' . ucfirst($type);
        return $this->{$convertMethod}($value);
    }

    /**
     * Attempts to convert an integer into a DateTime object
     *
     * @param integer $value value
     * 
     * @return bool|DateTime
     */
    protected function convertInteger($value)
    {
        return date_create("@$value");
    }

    /**
     * Attempts to convert a string into a DateTime object
     *
     * @param string $value value
     * 
     * @return bool|DateTime
     */
    protected function convertString($value)
    {
        $date = \DateTime::createFromFormat($this->format, $value);

        // Invalid dates can show up as warnings (ie. "2007-02-99")
        // and still return a DateTime object.
        $errors = \DateTime::getLastErrors();
        if ($errors['warning_count'] > 0) {
            return false;
        }
        return $date;
    }
}