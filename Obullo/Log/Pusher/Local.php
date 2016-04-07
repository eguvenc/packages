<?php

namespace Obullo\Log\Pusher;

use Exception;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

/**
 * Send log data to "app/classes/Workers/Logger" class.
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Local implements PusherInterface, ContainerAwareInterface
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

        $worker = new \Workers\Logger;
        $worker->setContainer($container);
        $worker->fire($data);
    }

}
