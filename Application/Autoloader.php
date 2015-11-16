<?php

namespace Obullo\Application;

/**
 * Autoloader
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Autoloader
{
    /**
     * PSR-0 Autoloader
     * 
     * @param string $realname classname 
     *
     * @see http://www.php-fig.org/psr/psr-0/
     * 
     * @return void
     */
    public static function autoload($realname)
    {
        if (class_exists($realname, false)) {  // Don't use autoloader
            return;
        }
        $className = ltrim($realname, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className);

        if (strpos($fileName, 'Obullo') === 0) {     // Check is it Obullo Package ?
            include_once OBULLO .substr($fileName, 7). '.php';
            return;
        }
        include_once CLASSES .$fileName. '.php'; // Otherwise load it from user directory
    }

    /**
     * Register Obullo PSR-0 autoloader
     * 
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(__NAMESPACE__ . "\\Autoloader::autoload");
    }
}