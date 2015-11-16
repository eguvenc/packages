<?php

namespace Obullo\Filters;

/**
 * Abstract Filter Class
 * 
 * @category  Filters
 * @package   AbstractFilter
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/filter
 */
abstract class AbstractFilter
{
    /**
     * Obullo Filter Constants
     */
    const OCTAL = FILTER_FLAG_ALLOW_OCTAL;
    const HEX = FILTER_FLAG_ALLOW_HEX;
    const SCHEME = FILTER_FLAG_SCHEME_REQUIRED;
    const HOST = FILTER_FLAG_HOST_REQUIRED;
    const PATH = FILTER_FLAG_PATH_REQUIRED;
    const QUERY = FILTER_FLAG_QUERY_REQUIRED;
    const STRIP_LOW = FILTER_FLAG_STRIP_LOW;
    const STRIP_HIGH = FILTER_FLAG_STRIP_HIGH;
    const ENCODE_LOW = FILTER_FLAG_ENCODE_LOW;
    const ENCODE_HIGH = FILTER_FLAG_ENCODE_HIGH;
    const ENCODE_AMP = FILTER_FLAG_ENCODE_AMP;
    const NO_ENCODE_QUOTES = FILTER_FLAG_NO_ENCODE_QUOTES;
    const FRACTION = FILTER_FLAG_ALLOW_FRACTION;
    const THOUSAND = FILTER_FLAG_ALLOW_THOUSAND;
    const SCIENTIFIC = FILTER_FLAG_ALLOW_SCIENTIFIC;
    const V4 = FILTER_FLAG_IPV4;
    const V6 = FILTER_FLAG_IPV6;
    const NO_PRIV_RANGE = FILTER_FLAG_NO_PRIV_RANGE;
    
    /**
     * Get default configuration params for filters
     * 
     * @param mixed $default value
     * 
     * @return array
     */
    public static function getDefault($default)
    {
        return array(
            'options' => [
                'default' => $default,
            ]
        );
    }

    /**
     * Returns php flag integer
     * 
     * @param string $flag short name
     * 
     * @return integer|null
     */
    protected static function getFlag($flag)
    {
        if (empty($flag)) {
            return;
        }
        $class = 'Obullo\Filters\AbstractFilter::';
        if (strpos($flag, '|') > 0) {
            $exp = explode('|', trim($flag, '|'));
            $val = null;
            foreach ($exp as $value) {
                $val += constant($class .strtoupper($value));
            }
            return $val;
        }
        return constant($class . strtoupper($flag));
    }
}