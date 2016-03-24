<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;

class Help extends Controller
{
    /**
     * Execute command
     * 
     * @return void
     */
    public function index()
    {
        echo Console::logo("Welcome to Task Manager (c) 2016");
        echo Console::newline(1);

echo Console::help("Available commands:", true);
echo Console::newline(2);
echo Console::help("
log        : Follow the http log file.
log ajax   : Follow the ajax log file.
log cli    : Follow the cli log file.
log clear  : Clear all log data.
queue      : Queue control features.
app        : Domain maintenance control.
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