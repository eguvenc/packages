<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Controller;
use Obullo\Cli\Console;

class Help extends Controller
{
    /**
     * Execute command
     * 
     * @return void
     */
    public function index()
    {
        echo Console::logo("Welcome to Task Manager (c) 2015");
        echo Console::description("You are running \$php task help command. For more help type php task [command] help.");

echo Console::help("Available commands:", true);
echo Console::newline(2);
echo Console::help("
log        : Follow the application log file.
log clear  : Clear all log data.
queue      : Queue control functions.
domain     : Domain maintenance control.
help       : See list all of available commands."
);
echo Console::newline(2);
echo Console::help("Usage:", true);
echo Console::newline(2);
echo Console::help("php task [command] [arguments]");
echo Console::newline(2);
echo Console::help("php task [command] --help");
echo Console::newline(2);
    }

}