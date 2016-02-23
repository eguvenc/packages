<?php

namespace Obullo\Log;

use Obullo\Queue\JobInterface;

use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Send log data to queue to listen log events from "app/classes/Workers/Logger" class.
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Queue implements JobInterface, ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Fire the job
     * 
     * @param mixed $job  object|null
     * @param array $data log data
     * 
     * @return void
     */
    public function fire($job, array $data)
    {
        $container = $this->getContainer();

        $queue =  $container->get('queue');
        $params = $container->get('logger.params');

        $queue->push(
            'Workers@Logger',
            $job->getName(),
            $data,
            $params['queue']['delay']
        );
    }

}
