<?php

namespace Obullo\Filters;

/**
 * Filter Is valid Class
 *
 * Also we use this class in request package like : $price = this->request->post('price', 'is')->int();
 * 
 * @category  Filters
 * @package   Is
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/filter
 */
class Is extends AbstractFilter
{
    /**
     * Validate integer value
     * 
     * @param mixed   $value   actual data
     * @param mixed   $default value to return if the filter fails
     * @param integer $min     specifies the minimum integer value
     * @param integer $max     specifies the maximum integer value
     * @param integer $flag    php constants
     *
     * Flags:
     *
     * octal - allows octal number values - ( FILTER_FLAG_ALLOW_OCTAL )
     * hex   - allows hexadecimal number values - ( FILTER_FLAG_ALLOW_HEX )
     * 
     * @return mixed
     */
    public function int($value = '', $default = false, $min = 0, $max = PHP_INT_MAX, $flag = 'octal')
    {
        $options = static::getDefault($default);

        $options['options']['min_range'] = $min;
        $options['options']['max_range'] = $max;
        $options['options']['flags'] = static::getFlag($flag);

        return filter_var($value, FILTER_VALIDATE_INT, $options);
    }

    /**
     * Validate float value
     * 
     * @param mixed $value   actual data
     * @param mixed $default value to return if the filter fails
     * 
     * @return mixed
     */
    public function float($value = '', $default = false)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT, static::getDefault($default));
    }

    /**
     * Validate boolean value
     * 
     * @param mixed $value   actual data
     * @param mixed $default value to return if the filter fails
     * 
     * @return mixed
     */
    public function bool($value = '', $default = false)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, static::getDefault($default));
    }

    /**
     * Validate email
     * 
     * @param string $value   actual data
     * @param strin  $default value to return if the filter fails
     * 
     * @return mixed
     */
    public function email($value = '', $default = false)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL, static::getDefault($default));
    }
    
    /**
     * Validate ipv4 and ipv6 addresses
     * 
     * @param string  $value   actual data
     * @param string  $default value to return if the filter fails
     * @param integer $flag    php ip flag
     * 
     * @return mixed
     */
    public function ip($value = '', $default = false, $flag = null)
    {
        $options = static::getDefault($default);
        $options['flags'] = static::getFlag($flag);

        return filter_var($value, FILTER_VALIDATE_IP, $options);
    }

    /**
     * Validate url
     * 
     * @param string  $value   actual data
     * @param string  $default value to return if the filter fails
     * @param integer $flag    php url flag
     *
     * Flags:
     *
     * scheme - FILTER_FLAG_SCHEME_REQUIRED - URL must be RFC compliant (like http://example)
     * host   - FILTER_FLAG_HOST_REQUIRED - URL must include host name (like http://www.example.com)
     * path   - FILTER_FLAG_PATH_REQUIRED - URL must have a path after the domain name (like www.example.com/example1/)
     * query  - FILTER_FLAG_QUERY_REQUIRED - URL must have a query string (like "example.php?name=Peter&age=37")
     * 
     * @return mixed
     */
    public function url($value = '', $default = false, $flag = 'scheme')
    {
        $options = static::getDefault($default);
        $options['options']['flags'] = static::getFlag($flag);

        return filter_var($value, FILTER_VALIDATE_URL, $options);
    }
}