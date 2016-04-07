<?php

namespace Obullo\Log\Pusher;

use Exception;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

/**
 * Send log data to queue to listen log events from "app/classes/Workers/Logger" class.
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Queue implements PusherInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Push the data
     * 
     * @param array $data log data
     * 
     * @return void
     */
    public function push(array $data)
    {
        $container = $this->getContainer();
        $params = $container->get('logger.params');

        $container->get('queue')->push(
            'Workers@Logger',
            $params['queue']['job'],
            $data,
            $params['queue']['delay']
        );
    }

}
