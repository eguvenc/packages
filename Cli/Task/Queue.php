<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;
use Obullo\Utils\Process\Process;

class Queue extends Controller
{
    /**
     * Run command
     * 
     * @return void
     */
    public function index()
    {
        $this->help();
    }

    /**
     * Print logo
     * 
     * @return string
     */
    public function logo()
    {
        echo Console::logo("Welcome to Queue Manager (c) 2015");
        echo Console::description("You are running \$php task queue command. For help type php task queue --help.");
    }

    /**
     * Print console help
     *
     * @return string
     */
    public function help()
    {
        $this->logo();

echo Console::help("Help:", true);
echo Console::newline(1);
echo Console::help("

Available Commands

    show        : Display all queued jobs.
    listen      : Run worker & consume queued jobs.
    help        : Display help.

Arguments

    --worker : Sets queue worker class name & exchange.( Exchange )
    --job    : Sets queue name.   ( Route )

Optional

    --output    : Enables queue output and any possible worker exceptions. ( Designed for local environment  )
    --delay     : Sets delay time for uncompleted jobs.
    --memory    : Sets maximum allowed memory for current job.
    --timeout   : Sets time limit execution of the current job.
    --sleep     : If we have not job on the queue sleep the script for a given number of seconds.
    --attempt   : Sets the maximum number of times a job should be attempted.
    --env       : Sets your environment variable to job class.
    --host      : Sets a hostname if you need.
    --var       : Sets your custom variable if you need."
);
echo Console::newline(2);
echo Console::help("Usage for local:", true);
echo Console::newline(2);
echo Console::help("php task queue listen --worker=Workers@Logger --job=logger.1 --output=1");
echo Console::newline(2);
echo Console::help("Usage for production:", true);
echo Console::newline(2);
echo Console::help("php task queue listen --worker=Workers@Logger --job=logger.1 --memory=128 --delay=0 --sleep=3 --timeout=3 --attempt=4 --output=0");
echo Console::newline(2);
echo Console::help("Shortcuts:", true);
echo Console::newline(2);
echo Console::help("php task queue listen --w=Workers@Logger --j=logger.1 --m=128 --d=0 --s=3 --t=3 --a=4 --o=0");
echo Console::newline(2);
    }

    /**
     * List ( output ) queue data
     *
     * Example : php task queue show --w=Workers@Logger --j=logger.1
     * 
     * @return string
     */
    public function show()
    {
        $this->uri = $this->request->getUri();

        $this->logo();
        $break = "------------------------------------------------------------------------------------------";

        $exchange = $this->uri->argument('worker');
        $route = $this->uri->argument('job', null);  // Sets queue route key ( queue name )

        if (empty($exchange)) {
            echo Console::fail("Queue \"--worker\" (exchange) (--w) can't be empty.");
            exit;
        }
        if (empty($route)) {
            echo Console::fail("Queue \"--job\" (route) (--j) can't be empty.");
            exit;
        }
        echo Console::body("Following \"". $route."\" queue ... \n\n");

        echo Console::body($break. "\n");
        echo Console::body("Job ID  | Job Name            | Data \n");
        echo Console::body($break. "\n");

        $lines = '';
        while (true) {
            $job = $this->queue->pop($exchange, $route);  // !!! Get the last message from queue but don't mark it as delivered
            if (! is_null($job)) {
                $raw = json_decode($job->getRawBody(), true);
                $jobIdRepeat = 6 - strlen($job->getId());  // 999999
                if (strlen($job->getId()) > 6) {
                    $jobIdRepeat = 6;
                }
                $jobNameRepeat = 20 - strlen($raw['job']);
                if (strlen($raw['job']) > 20) {
                    $jobNameRepeat = 20;
                }
                $lines = Console::body($job->getId().str_repeat(' ', $jobIdRepeat).'  | ');
                $lines.= Console::body($raw['job'].str_repeat(' ', $jobIdRepeat).' | ');
                $lines.= Console::text(json_encode($raw['data'], true)."\n", 'yellow');
                $lines.= "\n";
                echo $lines;
            }
        }
    }

    /**
     * Listen Queue
     *
     * Example : 
     * php task queue listen --worker=Workers@Logger --job=logger.1 --memory=128 --delay=0 --timeout=3 --sleep=0 --tries=0 --output=0 --env=production
     * 
     * @return void
     */
    public function listen()
    {
        $this->uri = $this->request->getUri();
        
        $output   = $this->uri->argument('output', 0);        // Enable / Disabled console output.
        $exchange = $this->uri->argument('worker', null);     // Sets queue worker / exchange
        $route    = $this->uri->argument('job', null);        // Sets queue job name / route key ( queue name )
        $memory   = $this->uri->argument('memory', 128);      // Sets maximum allowed memory for current job.
        $delay    = $this->uri->argument('delay', 0);         // Sets job delay interval
        $timeout  = $this->uri->argument('timeout', 0);       // Sets time limit execution of the current job.
        $sleep    = $this->uri->argument('sleep', 3);         // If we have not job on the queue sleep the script for a given number of seconds.
        $attempt  = $this->uri->argument('attempt', 0);       // If job attempt failed we push back on to queue and increase attempt number.
        $env      = $this->uri->argument('env');              // Sets environment for current worker.
        $host     = $this->uri->argument('host');             // Sets a hostname or server ip.
        $var      = $this->uri->argument('var', null);        // Sets your custom variable
        
        if (empty($exchange)) {
            echo Console::fail("Queue \"--worker\" (exchange) (--w) can't be empty.");
            exit;
        }
        if (empty($route)) {
            echo Console::fail("Queue \"--job\" (route) (--j) can't be empty.");
            exit;
        } 
        $cmd = "php task worker run --worker=$exchange --job=$route --memory=$memory --delay=$delay --timeout=$timeout --sleep=$sleep --attempt=$attempt --output=$output --env=$env";

        if ($host) {
            $cmd." --host=$host";
        }
        if ($var) {
            $cmd." --var=$var";
        }
        $process = new Process($cmd, ROOT, null, null, $timeout);
        while (true) {
            $process->run();
            if ($output == 1) {
                echo $process->getOutput();
            }
        }
    }

}