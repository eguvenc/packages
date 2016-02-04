<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use FilesystemIterator;
use Obullo\Cli\Controller;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Module extends Controller
{
    /**
     * Print Logo
     * 
     * @return string colorful logo
     */
    public function logo() 
    {
        echo Console::logo("Welcome to Module Manager (c) 2016");
        echo Console::description("Add / Remove modules. For more help type \$php task module help.");
    }

    /**
     * Add a new module
     *
     * @param string $module name
     * 
     * @return void
     */
    public function add($module = null)
    {   
        $this->uri = $this->request->getUri();

        $module = (empty($module)) ? strtolower($this->uri->argument('name')) : $module;

        if (empty($module)) {
            echo Console::fail("Module name can't be empty.");
            return;
        }
        $moduleFolder = OBULLO .'Application/Modules/' .$module;

        if (is_dir(MODULES .$module)) {
            echo Console::fail("Module #$module already exist in .modules/ folder.");
            return;
        }
        if (! is_dir($moduleFolder)) {
            echo Console::fail("Module #$module does not exist in Application/Modules folder.");
            return;
        }
        if (! is_writable(MODULES)) {
            echo Console::fail("We could not create directory in modules folder please check your write permissions.");
            return;
        }
        if (is_dir($moduleFolder.'/controllers')) {
            $this->recursiveCopy($moduleFolder. '/controllers', MODULES .$module);
        }
        if (is_dir($moduleFolder.'/tasks')) {
            $this->recursiveCopy($moduleFolder. '/tasks', TASKS, false);
        }
        $serviceFile = CONFIG .$this->container->get('app')->getEnv().'/providers/' .strtolower($module).'.php';

        if (is_dir($moduleFolder.'/providers')) {
            copy($moduleFolder.'/providers/'.strtolower($module).'.php', $serviceFile);
            chmod($serviceFile, 0777);
        }
        echo Console::success("New module #$module added successfully.");
    }

    /**
     * Remove 
     *
     * @param string $module name
     * 
     * @return void
     */
    public function remove($module = null)
    {
        $this->uri = $this->request->getUri();
        
        $module = (empty($module)) ? strtolower($this->uri->argument('name')) : $module;

        if (empty($module)) {
            echo Console::fail("Module name can't be empty.");
            return;
        }
        $moduleFolder = OBULLO .'Application/Modules/'. $module;

        if (! is_dir($moduleFolder)) {
            echo Console::fail("Module #$module does not exist in Application/Modules folder.");
            return;
        }
        if (! is_writable(MODULES)) {
            echo Console::fail("We could not remove directories in modules folder please check write permissions.");
            return;
        }
        if (is_dir($moduleFolder .'/controllers') && is_dir(MODULES .$module)) {
            $this->recursiveRemove(MODULES .$module);
            echo Console::success("Module #$module removed successfully.");
        }
        $serviceFile = CONFIG .$this->container->get('app')->getEnv().'/providers/' .strtolower($module).'.php';

        if (is_dir($moduleFolder .'/providers') && is_file($serviceFile)) {
            unlink($serviceFile);
            echo Console::success("Module #$module config removed successfully.");
        }
    }

    /**
     * Recursive copy
     * 
     * @param string $src   source
     * @param string $dst   destionation
     * @param string $mkdir mkdir option
     * 
     * @return void
     */
    protected function recursiveCopy($src, $dst, $mkdir = true)
    { 
        $dir = opendir($src);
        if ($mkdir) {
            @mkdir($dst); 
        }
        while (false !== ($file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if (is_dir($src .'/'. $file) ) {
                    $this->recursiveCopy($src .'/'. $file, $dst .'/'. $file);
                } else {
                    copy($src .'/'. $file, $dst .'/'. $file);
                    chmod($dst .'/'. $file, 0777);
                } 
            } 
        } 
        closedir($dir); 
    }

    /**
     * Remove directory and contents
     * 
     * @param string $dir full path of directory
     * 
     * @return void
     */
    protected function recursiveRemove($dir)
    {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($dir);
    }

    /**
     * Log help
     * 
     * @return string
     */
    public function help()
    {
        $this->logo();

echo Console::help("Help:", true);
echo Console::newline(2);
echo Console::help(
"Available Commands

    add      : Add new module to .modules/ directory.
    remove   : Remove module from .modules/ directory.");
echo Console::newline(2);
echo Console::help("Usage:", true);
echo Console::newline(2);
echo Console::help(
"php task module [command] name

    php task module add name
    php task module remove name");
echo Console::newline(2);
echo Console::help("Description:", true);
echo Console::newline(2);
echo Console::help("Add / remove modules to modules directory.");
echo Console::newline(2);
    }

}