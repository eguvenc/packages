<?php

namespace Obullo\View;

/**
 * View Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ViewInterface
{
    /**
     * Include nested view files from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string                      
     */
    public function load($filename, $data = null);

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = null);

    /**
     * Set variables
     * 
     * @param mixed $key view key => data or combined array
     * @param mixed $val mixed
     * 
     * @return void
     */
    public function assign($key, $val = null);

    /**
     * Get body / write & return to body
     * 
     * @param string  $_Vpath     full path
     * @param string  $_Vfilename filename
     * @param string  $_VData     mixed data
     * @param boolean $_VInclude  fetch as string or include
     * 
     * @return mixed
     */
    public function getBody($_Vpath, $_Vfilename, $_VData = null, $_VInclude = true);
    
}