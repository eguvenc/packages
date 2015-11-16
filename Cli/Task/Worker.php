<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Controller;
use Obullo\Queue\Worker as QueueWorker;

class Worker extends Controller
{
    /**
     * Run worker
     * 
     * @return void
     */
    public function run()
    {
        $worker = new QueueWorker(
            $this->c['app'],
            $this->c['config'],
            $this->c['queue'],
            $this->c['request']->getUri(),
            $this->c['logger']
        );
        $worker->init();
        $worker->pop();
    }
}