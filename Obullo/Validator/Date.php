<?php

namespace Obullo\Validator;

/**
 * Date Class
 * 
 * @category  Validator
 * @package   Date
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
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
     * Date check
     * 
     * @param string $str    string
     * @param string $format field
     * 
     * @return bool
     */    
    public function isValid($str, $format = 'Y-m-d')
    {   
        $this->value  = $str;
        $this->format = $format;
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