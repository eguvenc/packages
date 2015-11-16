<?php

namespace Obullo\View;

/**
 * Template Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface TemplateInterface
{
    /**
     * Include template file from /resources/templates folder
     * 
     * @param string $filename name
     * @param array  $data     data
     * 
     * @return string
     */
    public function load($filename, $data = null);

    /**
     * Get template files as string
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return object Stream
     */
    public function get($filename, $data = null);

    /**
     * Make template files as Stream body
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return object Stream
     */
    public function make($filename, $data = null);

    /**
     * Set variables
     * 
     * @param mixed $key view key => data or combined array
     * @param mixed $val mixed
     * 
     * @return void
     */
    public function assign($key, $val = null);

}