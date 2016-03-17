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
     * Current config folder
     * 
     * @var string
     */
    protected $path;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Main config file
     * 
     * @var array
     */
    protected $base = array();

    /**
     * Array stack
     * 
     * @var array
     */
    protected $array = array();

    /**
     * Enviromemt
     * 
     * @var string
     */
    protected static $env;

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
        self::$env = $container->get('env')->getValue();
        
        $this->path  = CONFIG .self::$env.'/';
        $this->local = CONFIG .'local/';
        $this->base = $this->array = include $this->local .'config.php';  // Load current environment config variables 

        if (self::$env != 'local') {
            
            $envConfig   = include $this->path .'config.php';
            $this->array = array_replace_recursive($this->array, $envConfig);  // Merge config variables if env not local.
        }
        $this->array['maintenance'] = include $this->path .'maintenance.php';
    }

    /**
     * Load Config File
     *
     * @param string $filename the config file name
     * 
     * @return array if the file was loaded correctly
     */
    public function load($filename)
    {
        $filename  = self::replaceFolder($filename);
        $container = $this->container; //  Make available $container variable in config files.

        if (isset($this->array[$filename])) {   // Is file loaded before ?
            return $this->array[$filename];
        }
        if ($filename == 'config') {  //  Config already loaded but someone may want to load it again.
            return $this->base;
        }
        $envFile = $this->path . $filename.'.php';
        $file = $this->local . $filename.'.php';  // Default config path

        $isEnvFile = false;
        if (is_file($envFile)) {   // Do we able to locate environment file ?
            $isEnvFile = true;
            $file = $envFile;
        }
        $config = include $file;

        if (self::$env != 'local' && $isEnvFile) { // Merge config variables if env not local.
            $localConfig = include $this->local . $filename .'.php';
            return $this->array[$filename] = array_replace_recursive($localConfig, $config);
        } else {
            $this->array[$filename] = $config;
        }
        return $this->array[$filename];
    }

    /**
     * Get main configuration file
     * 
     * @return array
     */
    public function base()
    {
        return $this->base;
    }

    /**
     * Save array data config file
     *
     * @param string $filename full path of the file
     * @param array  $data     config data
     * 
     * @return void
     */
    public function write($filename, array $data)
    {
        $filename = self::replaceFolder($filename);
        $fullpath = CONFIG .self::$env. '/';

        $writer = new PhpArray;
        $writer->toFile($fullpath . $filename.'.php', $data);
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
        return str_replace('::', '/', $filename);  // Folder support e.g. cache::redis 
    }

    /**
     * Sets a parameter or an object.
     *
     * @param string $key   The unique identifier for the parameter
     * @param mixed  $value The value of the parameter
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->array[$key] = $value;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return mixed The value of the parameter or an object
     */
    public function offsetGet($key)
    {
        if (! isset($this->array[$key])) {
            return false;
        }
        return $this->array[$key];
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return Boolean
     */
    public function offsetExists($key)
    {
        return isset($this->array[$key]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->array[$key]);
    }

}