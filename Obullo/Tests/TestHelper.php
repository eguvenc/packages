<?php

namespace Obullo\Tests;

use DateTime;

/**
 * Test helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestHelper
{
    /**
     * Returns to content type
     * 
     * @param object $request request
     * 
     * @return string
     */
    public static function getContentType($request)
    {
        $query  = $request->getQueryParams();
        $server = $request->getServerParams();

        if (defined('STDIN') && ! empty($server['argv'][0]) && strpos($server['argv'][0], "test") === 0) {
            return 'console';
        }
        return isset($query['response']) ? $query['response'] : 'html';
    }

    /**
     * Returns to class methods
     *
     * @param object $controller test
     * 
     * @return array
     */
    public static function getClassMethods($controller)
    {
        $disabledMethods = [
            'index',
            '__construct',
            '__get',
            '__set',
            '__generateHtmlResponse',
            '__generateConsoleResponse',
            '__generateTestResults',
            'assertTrue',
            'assertFalse',
            'assertNull',
            'assertEqual',
            'assertEmpty',
            'assertNotEmpty',
            'assertNotEqual',
            'assertInstanceOf',
            'assertArrayHasKey',
            'assertArrayNotHasKey',
            'assertContains',
            'assertGreaterThan',
            'assertLessThan',
            'assertInternalType',
            'assertNotInternalType',
            'assertNotType',
            'assertObjectHasAttribute',
            'assertObjectNotHasAttribute',
            'assertThat',
            'assertArrayContains',
            'assertStringContains',
            'assertDate',
        ];
        $disabledMethods = array_merge($disabledMethods, TestPreferences::getIgnoredMethods());
        $methods = array();
        foreach (get_class_methods($controller) as $name) {
            if (! in_array($name, $disabledMethods)) {
                $methods[] = $name;
            }
        }
        return $methods;
    }

    /**
     * Get class methods as html
     *
     * @param object $controller controller
     * @param object $container  container
     *  
     * @return string|array
     */
    public static function getHtmlClassMethods($controller, $container)
    {
        $html = "";
        $url = $container->get('url');
        $request = $container->get('request');
        foreach (self::getClassMethods($controller) as $name) {
            $html.= $url->anchor(rtrim($request->getUri()->getPath(), "/")."/".$name, $name)."<br>";
        }
        return $html;
    }

     /**
     * Attempts to convert an int, string, or array to a DateTime object
     *
     * @param string|int|array $value value
     * 
     * @return bool
     */
    public static function convertToDateTime($value)
    {
        if ($value instanceof DateTime) {
            return true;
        }
        if (is_integer($value)) {
            $value = @date("Y-m-d H:i:s", $value);
        }
        return date_create("$value");
    }

    /**
     * Get internal type
     * 
     * @param string $expected value
     * @param mixed  $actual   value
     * 
     * @return boolean
     */
    public static function checkType($expected, $actual)
    {
        switch ($expected) {
        case ($expected == 'integer' || $expected == 'int'):
            $pass = is_int($actual);
            break;
        case ($expected == 'boolean' || $expected == 'bool'):
            $pass = is_bool($actual);
            break;
        case ($expected == 'string'):
            $pass = is_string($actual);
            break;
        case ($expected == 'object'):
            $pass = is_object($actual);
            break;    
        case ($expected == 'float'):
            $pass = is_float($actual);
            break;
        case ($expected == 'double'):
            $pass = is_double($actual);
            break;
        case ($expected == 'resource'):
            $pass = is_resource($actual);
            break;
        case ($expected == 'null'):
            $pass = is_null($actual);
            break;
        case ($expected == 'numeric'):
            $pass = is_numeric($actual);
            break;
        case ($expected == 'scalar'):
            $pass = is_scalar($actual);
            break;
        case ($expected == 'alpha'):
            $pass = ctype_alpha($actual);
            break;
        // case ($expected == 'alphaunicode'):
        //     $pass = "";
        //     break;
        case ($expected == 'alnum'):
            $pass = ctype_alnum($actual);
            break;
        // case ($expected == 'alnumunicode'):
        //     $pass = '';
        //     break;
        // case ($expected == 'digitunicode'):
        //     $pass = '';
        //     break;
        case ($expected == 'lower'):
            $pass = ctype_lower($actual);
            break;
        }
        return $pass;
    }


}