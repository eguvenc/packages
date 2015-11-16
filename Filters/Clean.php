<?php

namespace Obullo\Filters;

/**
 * Filter Sanitizer
 * 
 * Also we use this class in request package like : $price = this->request->post('price', 'clean')->float();
 * 
 * @category  Filters
 * @package   Clear
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/filter
 */
class Clean extends AbstractFilter
{
    /**
     * Removes all HTML tags from a string
     *
     * @param string $value actual data 
     * @param string $flag  constant
     *
     * Flags:
     * 
     * no_encode_quotes - FILTER_FLAG_NO_ENCODE_QUOTES - Do not encode quotes
     * strip_low - FILTER_FLAG_STRIP_LOW - Remove characters with ASCII value < 32
     * strip_high - FILTER_FLAG_STRIP_HIGH - Remove characters with ASCII value > 127
     * encode_low - FILTER_FLAG_ENCODE_LOW - Encode characters with ASCII value < 32
     * encode_high - FILTER_FLAG_ENCODE_HIGH - Encode characters with ASCII value > 127
     * encode_amp - FILTER_FLAG_ENCODE_AMP - Encode the "&" character to &amp;
     * 
     * @return string
     */
    public function str($value = '', $flag = 'strip_low')
    {
        return filter_var($value, FILTER_SANITIZE_STRING, static::getFlag($flag));
    }

    /**
     * This filter removes data that is potentially harmful for your application.
     * It is used to strip tags and remove or encode unwanted characters.
     * This filter does nothing if no flags are specified
     * 
     * @param string  $value actual data
     * @param integer $flag  constant
     *
     * Flags:
     * 
     * strip_low - FILTER_FLAG_STRIP_LOW - Strip characters with ASCII value below 32
     * strip_high - FILTER_FLAG_STRIP_HIGH - Strip characters with ASCII value above 32
     * encode_low - FILTER_FLAG_ENCODE_LOW - Encode characters with ASCII value below 32
     * encode_high - FILTER_FLAG_ENCODE_HIGH - Encode characters with ASCII value above 32
     * encode_amp  - FILTER_FLAG_ENCODE_AMP - Encode the & character to &amp;
     * 
     * @return string
     */
    public function raw($value = '', $flag = 'strip_low')
    {
        return filter_var($value, FILTER_UNSAFE_RAW, static::getFlag($flag));
    }

    /**
     * Removes all illegal characters from a number
     * 
     * @param string $value actual data
     * 
     * @return string
     */
    public function int($value = '')
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Removes all illegal characters from a float number
     *
     * fraction - FILTER_FLAG_ALLOW_FRACTION - Allow fraction separator (like . )
     * thousand - FILTER_FLAG_ALLOW_THOUSAND - Allow thousand separator (like , )
     * scientific - FILTER_FLAG_ALLOW_SCIENTIFIC - Allow scientific notation (like e and E)
     * 
     * @param string  $value actual data
     * @param integer $flag  constant
     * 
     * @return string
     */
    public function float($value = '', $flag = 'fraction')
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, static::getFlag($flag));
    }

    /**
     * Sanitize email
     * 
     * @param string $email actual data
     * 
     * @return string
     */
    public function email($email = '')
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Performs the addslashes() function to a string
     *
     * This filter sets backslashes in front of predefined characters:
     * 
     * single quote (')
     * double quote (")
     * backslash (\)
     * NULL
     * 
     * @param string $str actual data
     * 
     * @return string
     */
    public function quote($str = '')
    {
        return filter_var($str, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    /**
     * HTML-escapes special characters
     * 
     * @param string  $str  actual data
     * @param integer $flag constant
     *
     * strip_low - FILTER_FLAG_STRIP_LOW - Strip characters with ASCII value below 32
     * strip_high - FILTER_FLAG_STRIP_HIGH - Strip characters with ASCII value above 32
     * encode_high - FILTER_FLAG_ENCODE_HIGH - Encode characters with ASCII value above 32
     * 
     * @return string
     */
    public function escape($str = '', $flag = 'strip_low')
    {
        return filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS, static::getFlag($flag));
    }
    
    /**
     * Equivalent to calling htmlspecialchars() with ENT_QUOTES set. Encoding quotes can be 
     * disabled by setting FILTER_FLAG_NO_ENCODE_QUOTES.
     * 
     * Flags:
     *
     * FILTER_FLAG_NO_ENCODE_QUOTES
     * 
     * @param string  $html actual data
     * @param integer $flag constant
     * 
     * @see http://php.net/manual/en/filter.filters.sanitize.php
     * 
     * @return string
     */
    public function fullEscape($html = '', $flag = null) 
    {
        return filter_var($html, FILTER_SANITIZE_FULL_SPECIAL_CHARS, static::getFlag($flag));
    }

    /**
     * Removes all illegal URL characters from a string
     * 
     * This filter allows all letters, digits and $-_.+!*'(),{}|\\^~[]`"><#%;/?:@&=
     * 
     * @param string $url  actual data
     * @param string $flag constant
     *
     * scheme - FILTER_FLAG_SCHEME_REQUIRED - URL must be RFC compliant (like http://example)
     * host - FILTER_FLAG_HOST_REQUIRED - URL must include host name (like http://www.example.com)
     * path - FILTER_FLAG_PATH_REQUIRED - URL must have a path after the domain name (like www.example.com/example1/)
     * query - FILTER_FLAG_QUERY_REQUIRED - URL must have a query string (like "example.php?name=Peter&age=37")
     *
     * @return string
     */
    public function url($url = '', $flag = 'scheme')
    {
        return filter_var($url, FILTER_SANITIZE_URL,  static::getFlag($flag));
    }

    /**
     * Encode special characters in the $url variable
     *
     * Flags:
     *
     * strip_low - FILTER_FLAG_STRIP_LOW - Remove characters with ASCII value < 32
     * strip_high - FILTER_FLAG_STRIP_HIGH - Remove characters with ASCII value > 127
     * encode_low - FILTER_FLAG_ENCODE_LOW - Encode characters with ASCII value < 32
     * encode_high - FILTER_FLAG_ENCODE_HIGH - Encode characters with ASCII value > 127
     * 
     * @param string  $url  actual data
     * @param integer $flag constant
     * 
     * @return string
     */
    public function urlencode($url = '', $flag = 'strip_low')
    {
        return filter_var($url, FILTER_SANITIZE_ENCODED, static::getFlag($flag));
    }

}