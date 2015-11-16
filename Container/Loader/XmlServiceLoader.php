<?php

namespace Obullo\Container\Loader;

use SimpleXMLElement;

/**
 * Xml Service Loader for Obullo Container
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class XmlServiceLoader
{
    /**
     * Parse file content
     * 
     * @param string $file full path
     * 
     * @return array
     */
    public function load($file)
    {
        $xmlObject = simplexml_load_file($file);
        $config = self::xml2array($xmlObject);
        return $config;
    }

    /**
     * Call service methods
     * 
     * @param object $obj    service
     * @param array  $config configuration
     * 
     * @return void
     */
    public function callMethods($obj, array $config)
    {
        if (isset($config['methods']['method'])) {

            foreach ($config['methods'] as $methods) {
                if (isset($methods['@attributes']['name'])) {           // Single method
                    $name = (string)$methods['@attributes']['name'];
                    $args = (array)$methods['argument'];
                    if (method_exists($obj, $name)) {
                        call_user_func_array(array($obj, $name), $args); // All arguments must be array
                    }
                } elseif (isset($methods[0]) && $methods[0] instanceof SimpleXMLElement) {  // Multiple method support

                    foreach ($methods as $func) {
                        $name = (string)$func->attributes()->name;
                        $args = (array)$func->argument;
                        if (method_exists($obj, $name)) {
                            call_user_func_array(array($obj, $name), $args); // All arguments must be array
                        }
                    }
                }
            }
        }
    }

    /**
     * Convers xml object to array
     * 
     * @param object $xmlObject xml
     * @param array  $out       output
     * 
     * @return array
     */
    protected static function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node ) {
            $out[$index] = (is_object($node)) ? self::xml2array($node) : $node;
        }
        return $out;
    }

}