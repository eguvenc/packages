<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;

class Log extends Controller
{
    /**
     * Index
     * 
     * @param string $direction http / ajax / cli
     * 
     * @return void
     */
    public function index($direction = 'http') 
    {
        $this->logo();

        $this->uri = $this->request->getUri();
        $reader = ucfirst($this->logger->getWriter());

        if ($reader == 'Null') {
            echo Console::text("Logging feature disabled from your config.", 'yellow');
            echo Console::newline(2);
            return;
        }
        $Class = '\\Obullo\Cli\LogReader\\'.$reader;
        $class = new $Class;
        $class->follow($this->c, $direction);
    }

    /**
     * Follow http log data
     * 
     * @return void
     */
    public function http()
    {
        $this->index(__FUNCTION__);
    }

    /**
     * Follow ajax log
     * 
     * @return void
     */
    public function ajax()
    {
        $this->index(__FUNCTION__);
    }

    /**
     * Follow cli log data
     * 
     * @return void
     */
    public function cli()
    {
        $this->index(__FUNCTION__);
    }

    /**
     * Clear all log data from log folder
     *
     * Also removes queue data
     * 
     * @return void
     */
    public function clear()
    {
        $files = \Obullo\Cli\LogReader\File::getPathArray();

        foreach ($files as $file) {
            $file = ROOT. $file;
            $exp = explode('/', $file);
            $filename = array_pop($exp);
            $path = implode('/', $exp). '/';

            if (is_file($path.$filename)) {
                unlink($path.$filename);
            }
        }
        echo Console::success('Application logs deleted.');
    }

    /**
     * Print Logo
     * 
     * @return string colorful logo
     */
    public function logo() 
    {
        echo Console::logo("Welcome to Log Manager (c) 2015");
        echo Console::description("You are displaying log data. For more help type \$php task log help.");
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

    clear    : Clear log data ( also removes the queue logs ).
    help     : Help
    http     : Follow http logs.
    ajax     : Follow ajax logs.
    cli      : Follow console logs.");

echo Console::newline(2);
echo Console::help("Usage:",true);
echo Console::newline(2);
echo Console::help(
"php task log --dir=value

    php task log
    php task log http
    php task log cli
    php task log ajax");
echo Console::newline(2);

echo Console::help("Description:", true);
echo Console::newline(2);
echo Console::help("Read log data from '". RESOURCES ."data/logs' folder.");
echo Console::newline(2);
    }

}