<?php

namespace Obullo\Log\Pusher;

use AMQPQueue;
use AMQPChannel;
use AMQPExchange;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

/**
 * Send log data to queue to listen log events from "app/classes/Workers/Logger" class.
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Amqp implements PusherInterface, ContainerAwareInterface
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
        // http://php.net/manual/pl/amqp.constants.php

        $container  = $this->getContainer();
        $connection = $container->get('amqp')->shared(['connection' => 'default']);
        
        $params     = $container->get('logger.params');
        $routingKey = $params['queue']['job'];
        $payload    = json_encode(array('job' => $routingKey, 'data' => $data));

        $channel = new AMQPChannel($connection);

        $exchangeName = 'Workers@Logger';
        $exchange = new AMQPExchange($channel);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setName($exchangeName);
        $exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setName($routingKey);
        $queue->declareQueue();
        $queue->bind($exchangeName, $routingKey);
        $exchange->publish($payload, $routingKey, AMQP_MANDATORY);

        $connection->disconnect();

    }

}
