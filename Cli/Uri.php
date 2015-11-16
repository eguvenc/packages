<?php

namespace Obullo\Cli;

use Obullo\Log\LoggerInterface;

/**
 * Console Uri
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Uri implements UriInterface
{
    /**
     * Argument seperator
     */
    const SEPARATOR = '=';

    /**
     * Argument sign
     */
    const SIGN = '--';

    /**
     * All cli arguments not values
     * 
     * @var array
     */
    protected $segments;

    /**
     * All cli arguments with key and values ( associative array )
     * 
     * @var array
     */
    protected $arguments;

    /**
     * Short commands
     * 
     * @var array
     */
    protected static $shortcuts = [
        '--w=' => '--worker=',
        '--j=' => '--job=',
        '--d=' => '--delay=',
        '--m=' => '--memory=',
        '--s=' => '--sleep=',
        '--t=' => '--timeout=',
        '--a=' => '--attempt=',
        '--h=' => '--host=',
        '--o=' => '--output=',
        '--v=' => '--var=',
        '--e=' => '--env='
    ]; 

    /**
     * Reset variables
     * 
     * @return void
     */
    public function clear()
    {
        $this->segments = array();
        $this->arguments = array();
    }

    /**
     * Resolve command line parameters
     * 
     * @return array resolved parameters
     */
    public function init()
    {
        $argv = $this->getPath();
        $parameters = explode("\n", $argv);
        
        if (! is_array($parameters) || ! isset($parameters[0])) {
            return array();
        }
        $params = array();
        foreach ($parameters as $value) {
            if (strpos($value, static::SIGN) === 0) {
                $val = explode(static::SEPARATOR, $value);
                $paramKey = trim($val[0], static::SIGN);
                $params[] = $paramKey;
                $this->arguments[$paramKey] = isset($val[1]) ? $val[1] : $paramKey;
            } else {
                $params[] = trim($value, static::SIGN);
            }
        }
        $this->segments = $params;
        return $params;
    }

    /**
     * Get one segment
     * 
     * @param mixed $segment integer number or string segment
     * @param mixed $default default value of segment
     * 
     * @return mixed valid segment or null
     */
    public function segment($segment, $default = null)
    {
        if (is_numeric($segment) && isset($this->segments[$segment])) {
            return $this->segments[$segment];
        }
        return $default;
    }

    /**
     * Get all segments
     * 
     * @return array all segments
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * Get one argument
     * 
     * @param mixed $key     key of argument
     * @param mixed $default default value of argument
     * 
     * @return mixed valid argument or null
     */
    public function argument($key, $default = null)
    {
        if (is_string($key) && isset($this->arguments[$key])) {
            return $this->arguments[$key];
        }
        return $default;
    }

    /**
     * Get all arguments
     * 
     * @return array all arguments
     */
    public function getArguments() 
    {
        return $this->arguments;
    }

    /**
     * Get executed original command with parameters
     *
     * @param boolean $nl whether to use newline
     * 
     * @return string
     */
    public function getPath($nl = true)
    {
        $shortcuts = $this->getShortcuts();
        $cmdString = implode("\n", array_slice($_SERVER['argv'], 1));
        $cmdString = str_replace(
            array_keys($shortcuts),
            array_values($shortcuts),
            $cmdString
        );
        if ($nl == false) {
            $cmdString = str_replace("\n", "/", $cmdString);
        }
        return $cmdString;
    }

    /**
     * Returns to all argument shortcuts
     * 
     * @return array
     */
    public function getShortcuts()
    {
        return static::$shortcuts;
    }

}