<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;
use Obullo\Tests\Util\TestSuite;

class Test extends Controller
{
    /**
     * Execute command
     * 
     * @return void
     */
    public function index()
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);

        $this->help();
    }

    /**
     * Display help
     * 
     * @return void
     */
    public function help()
    {
        echo Console::help("Available commands:", true);
        echo Console::newline(2);
        echo Console::help("test file   : Test single file.
test folder : Test folders.
help        : See list all of available commands."
        );
        echo Console::newline(2);
        echo Console::help("Usage:", true);
        echo Console::newline(2);
        echo Console::help("php task file /tests/foldername/filename");
        echo Console::newline(2);
        echo Console::help("php task folder /tests/foldername");
        echo Console::newline(2);
        echo Console::help("php task folder");
        echo Console::newline(2);
    }

    /**
     * Test class
     * 
     * @param string $class name
     * 
     * @return void
     */
    public function file($class = null)
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);

        if (empty($class)) {
            return $this->index();
        }
        TestSuite::fileTest($class);
    }

    /**
     * Folders
     * 
     * @param string $folder folder
     * 
     * @return void
     */
    public function folder($folder = null)
    {
        TestSuite::folderTest($folder);
    }

}