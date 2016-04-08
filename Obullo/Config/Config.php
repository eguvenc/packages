<?php

namespace Obullo\Config;

use Obullo\Config\Writer\PhpArray;
use Interop\Container\ContainerInterface as Container;

/**
 * Config Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Config implements ConfigInterface
{
    /**
     * Folder Separator
     */
    const FOLDER_SEPARATOR = '::';

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Array stack
     * 
     * @var array
     */
    protected $array = array();

    /**
     * Constructor
     *
     * Sets the $config data from the primary config.php file as a class variable
     * 
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Load Config File
     *
     * @param string $filename the config file name
     * 
     * @return array if the file was loaded correctly
     */
    public function get($filename)
    {
        $filename  = self::replaceFolder($filename);
        $container = $this->container; //  Make available $container variable in config files.

        if (isset($this->array[$filename])) {   // Is file loaded before ?
            return $this->array[$filename];
        }
        $envFile = CONFIG .$container->get('env')->getValue().'/' . $filename.'.php';
        $file    = CONFIG .'local/' . $filename.'.php';

        $isEnvFile = false;
        if (is_file($envFile)) {   // Do we able to locate environment file ?
            $isEnvFile = true;
            $file = $envFile;
        }
        $config = include $file;
        /**
         * Merge config variables if env not local.
         */
        if ($container->get('env')->getValue() != 'local' && $isEnvFile) {
            return $this->array[$filename] = array_replace_recursive(include CONFIG .'local/' . $filename .'.php', $config);
        } else {
            $this->array[$filename] = $config;
        }
        return $this->array[$filename];
    }

    /**
     * Save array data config file
     *
     * @param string $filename full path of the file
     * @param array  $data     config data
     * 
     * @return array data
     */
    public function write($filename, array $data)
    {
        $filename = self::replaceFolder($filename);
        $fullpath = CONFIG .$this->container->get('env')->getValue(). '/';

        $writer = new PhpArray;
        $writer->toFile($fullpath . $filename.'.php', $data);

        unset($this->array[$filename]); // Remove cache to reload file again.

        return $data;
    }

    /**
     * Convert "::"" to "/"
     * 
     * @param string $filename filename
     * 
     * @return string
     */
    protected static function replaceFolder($filename)
    {
        return str_replace(static::FOLDER_SEPARATOR, '/', $filename);  // Folder support e.g. cache::redis 
    }

}