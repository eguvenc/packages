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
            $this->container->get('app'),
            $this->container->get('config'),
            $this->container->get('queue'),
            $this->container->get('request')->getUri(),
            $this->container->get('logger')
        );
        $worker->init();
        $worker->pop();
    }
}