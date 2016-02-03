<?php

namespace Obullo\Validator\Rules;

use DateTime;
use Obullo\Validator\FieldInterface as Field;

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
     * Format
     * 
     * @var string
     */
    public $format = 'Y-m-d';

    /**
     * Call next
     * 
     * @param Field    $field object
     * @param Callable $next  object
     * 
     * @return objec
     */
    public function __invoke(Field $field, Callable $next)
    {
        $this->format = (string)$field->getRule()->getParam(0, 'Y-m-d');
        
        return (! $this->convertToDateTime($field->getValue())) ? false : $next($field);
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
        if ($value instanceof DateTime) {
            return $value;
        }
        $type = gettype($value);
        if (! in_array($type, array('string', 'integer'))) {
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
        $date = DateTime::createFromFormat($this->format, $value);

        // Invalid dates can show up as warnings (ie. "2007-02-99")
        // and still return a DateTime object.
        $errors = DateTime::getLastErrors();
        
        if ($errors['warning_count'] > 0) {
            return false;
        }
        return $date;
    }
}