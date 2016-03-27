<?php

namespace Obullo\Tests;

/**
 * Test preferences
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestPreferences {

    /**
     * Ignored method
     * 
     * @var array
     */
    protected static $ignoredMethods = array();

    /**
     * Ignore console access
     * 
     * @var boolean
     */
    protected static $console = true;

    /**
     * Ignore console
     * 
     * @param string $prop property
     * 
     * @return void
     */
    public static function ignoreProperty($prop)
    {
        self::${$prop} = false;
    }

    /**
     * Set ignored method
     * 
     * @param string|array $method method name(s)
     * 
     * @return void
     */
    public static function ignoreMethod($method)
    {
        if (is_array($method)) {
            foreach ($method as $name) {
                self::$ignoredMethods[] = $name;
            }
        } else {
            self::$ignoredMethods[] = $method;
        }
    }

    /**
     * Returns to ignored method array
     * 
     * @return array
     */
    public static function getIgnoredMethods()
    {
        return self::$ignoredMethods;
    }

    /**
     * Check option is ignored
     * 
     * @param string $type type
     * 
     * @return boolean
     */
    public static function isIgnored($type = 'console')
    {
        $ignored = self::${$type};

        if ($ignored == false) {
            return true;
        }
        return false;
    }

}