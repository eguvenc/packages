<?php

namespace Obullo\Cli;

/**
 * Uri Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface UriInterface
{
    /**
     * Reset variables
     * 
     * @return void
     */
    public function clear();

    /**
     * Resolve command line parameters
     * 
     * @return array resolved parameters
     */
    public function init();

    /**
     * Get one segment
     * 
     * @param mixed $segment integer number or string segment
     * @param mixed $default default value of segment
     * 
     * @return mixed valid segment or null
     */
    public function segment($segment, $default = null);

    /**
     * Get all segments
     * 
     * @return array all segments
     */
    public function getSegments();

    /**
     * Get one argument
     * 
     * @param mixed $key     key of argument
     * @param mixed $default default value of argument
     * 
     * @return mixed valid argument or null
     */
    public function argument($key, $default = null);

    /**
     * Get all arguments
     * 
     * @return array all arguments
     */
    public function getArguments();

    /**
     * Get executed original command with parameters
     *
     * @param boolean $nl whether to use newlines
     * 
     * @return string
     */
    public function getPath($nl = true);

    /**
     * Returns to all argument shortcuts
     * 
     * @return array
     */
    public function getShortcuts();
    
}