<?php

namespace Obullo\Container\Loader;

/**
 * Php Service Loader for Obullo Container
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PhpServiceLoader
{
    /**
     * Parse file content
     * 
     * @param string $file full path
     * 
     * @return array
     */
    public static function load($file)
    {
        $config = include $file;
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
        if (isset($config['methods'])) {
            foreach ($config['methods'] as $func) {
                foreach ($func as $method => $args) {
                    if (method_exists($obj, $method)) {
                        call_user_func_array(array($obj, $method), (array)$args); // All arguments must be array
                    }
                }
            }
        }
    }

}