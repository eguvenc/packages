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
     * @param string $name name
     * @param array  $data data
     * 
     * @return string
     */
    public function make($name, $data = array())
    {
        extract($data);

        ob_start();
        include $this->getDefaultPath() . $name . '.php';
        $body = ob_get_clean();
        
        return $body;
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