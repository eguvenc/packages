<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Controller;

class Worker extends Controller
{
    /**
     * Run worker
     * 
     * @return void
     */
    public function run()
    {
        $worker = new \Obullo\Queue\Worker(
            $this->container->get('config'),
            $this->container->get('queue'),
            $this->container->get('request')->getUri(),
            $this->container->get('logger')
        );
        $worker->init();
        $worker->pop();
    }
}