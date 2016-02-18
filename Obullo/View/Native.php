<?php

namespace Obullo\View;

use League\Container\ImmutableContainerAwareInterface;
use League\Container\ImmutableContainerAwareTrait;

/**
 * Default engine
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Native implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Default path
     * 
     * @var string
     */
    protected $path;

    /**
     * View folders
     * 
     * @var array
     */
    protected $folders = array();

    /**
     * Protected variables
     * 
     * @var array
     */
    protected $_boolStack   = array();    // Boolean type view variables
    protected $_arrayStack  = array();    // Array type view variables
    protected $_stringStack = array();    // String type view variables
    protected $_objectStack = array();    // Object type view variables

    /**
     * Constructor
     * 
     * @param stirng $path default
     */
    public function __construct($path)
    {
        $this->path = $path.'/';
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     * 
     * @param string $name      name
     * @param string $directory folder
     * 
     * @return Engine
     */
    public function addFolder($name, $directory)
    {
        $this->folders[$name] = $directory;

        return $this;
    }

    /**
     * Remove a template folder.
     * 
     * @param string $name name
     * 
     * @return Engine
     */
    public function removeFolder($name)
    {
        unset($this->folders[$name]);

        return $this;
    }

    /**
     * Get collection of all template folders.
     * @return Folders
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Create a new template and render it.
     * 
     * @param string $name name
     * @param array  $data data
     * 
     * @return string
     */
    public function render($name, array $data = array())
    {
        $name = $this->renderFilename($name);

        return $this->make($name, $data);
    }
    
    /**
     * Render filename
     * 
     * @param string $name filename
     * 
     * @return string
     */
    protected function renderFilename($name)
    {
        if (strpos($name, '::') > 0) {  // Folder support.

            $this->path = '';  // Reset path variable.
            $parts = explode('::', $name);

            return rtrim($this->getFolderPath($parts[0]), '/').'/'.$parts[1];
        }
        return $name;
    }

    /**
     * Returns to folder path
     * 
     * @param string $name filename
     * 
     * @return string path
     */
    protected function getFolderPath($name)
    {
        $folders = $this->getFolders();

        return $folders[$name];
    }

    /**
     * Returns to default view path
     * 
     * @return string
     */
    protected function getDefaultPath()
    {
        return $this->path;
    }

    /**
     * Make
     * 
     * @param string $filename filename
     * @param array  $data     data
     * 
     * @return string
     */
    public function make($filename, $data = null)
    {
        $this->assignVariables($data);

        extract($this->_stringStack, EXTR_SKIP);
        extract($this->_arrayStack, EXTR_SKIP);
        extract($this->_objectStack, EXTR_SKIP);
        extract($this->_boolStack, EXTR_SKIP);

        ob_start();
        include $this->getDefaultPath() . $filename . '.php';
        $body = ob_get_clean();
        
        return $body;
    }

    /**
     * Assign view variables
     * 
     * @param array $data view data
     * 
     * @return void
     */
    protected function assignVariables($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->assign($key, $value);
            }
        }
    }

    /**
     * Set variables
     * 
     * @param mixed $key view key => data or combined array
     * @param mixed $val mixed
     * 
     * @return void
     */
    protected function assign($key, $val = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->assignVar($k, $v);
            }
        } else {
            $this->assignVar($key, $val);
        }
    }

    /**
     * Set variables
     * 
     * @param string $key view key data
     * @param mixed  $val mixed
     * 
     * @return void
     */
    protected function assignVar($key, $val)
    {
        if (is_int($val)) {
            $this->_stringStack[$key] = $val;
            return;
        }
        if (is_string($val)) {
            $this->_stringStack[$key] = $val;
            return;
        }
        $this->_arrayStack[$key] = array();  // Create empty array
        if (is_array($val)) {
            if (count($val) == 0) {
                $this->_arrayStack[$key] = array();
            } else {
                foreach ($val as $array_key => $value) {
                    $this->_arrayStack[$key][$array_key] = $value;
                }
            }
        }
        if (is_object($val)) {
            $this->_objectStack[$key] = $val;
            $this->_arrayStack = array();
            return;
        }
        if (is_bool($val)) {
            $this->_boolStack[$key] = $val;
            $this->_arrayStack = array();
            return;
        }
        $this->_stringStack[$key] = $val;
        $this->_arrayStack = array();
        return;
    }

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->getContainer()->get($key);
    }

}