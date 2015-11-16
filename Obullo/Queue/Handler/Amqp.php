<?php

namespace Obullo\Queue\Handler;

use AMQPQueue;
use AMQPChannel;
use AMQPEnvelope;
use AMQPExchange;
use AMQPException;
use AMQPConnection;
use RuntimeException;
use Obullo\Queue\QueueInterface;
use Obullo\Config\ConfigInterface;
use Obullo\Queue\JobHandler\AmqpJob;
use Obullo\Container\ServiceProviderInterface;

/**
 * Info
 * 
 * @see http://www.php.net/manual/pl/book.amqp.php
 * @see http://www.brandonsavage.net/publishing-messages-to-rabbitmq-with-php/
 */

/**
 * AMQP Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Amqp implements QueueInterface
{
    /**
     * AMQP channel name
     * 
     * @var string
     */
    protected $channel = null;

    /**
     * AMQP connection instance
     * 
     * @var object
     */
    protected $AMQPconnection;

    /**
     * Default queue name if its not provided
     * 
     * @var string
     */
    protected $defaultQueueName;

    /**
     * Constructor
     *
     * @param object $config   \Obullo\Config\ConfigInterface
     * @param object $provider \Obullo\Service\Provider\ServiceProviderInterface 
     * @param array  $params   provider parameters
     */
    public function __construct(ConfigInterface $config, ServiceProviderInterface $provider, array $params)
    {
        $this->config = $config->load('queue')['amqp'];
        $this->AMQPconnection = $provider->get($params);
        
        $this->channel = new AMQPChannel($this->AMQPconnection);
        $this->defaultQueueName = 'default';
    }

    /**
     * Create AMQPExchange if not exists otherswise get instance of it
     * 
     * @param object $name exchange name
     *
     * @return object
     */
    protected function declareExchange($name)
    {
        // available types AMQP_EX_TYPE_DIRECT, AMQP_EX_TYPE_FANOUT, AMQP_EX_TYPE_HEADER or AMQP_EX_TYPE_TOPIC,
        // available flags AMQP_DURABLE, AMQP_PASSIVE

        $type = constant('AMQP_EX_TYPE_'.strtoupper($this->config['exchange']['type']));
        $flag = constant('AMQP_'.strtoupper($this->config['exchange']['flag']));

        $this->exchange = new AMQPExchange($this->channel);
        $this->exchange->setName($name);
        $this->exchange->setFlags($flag);
        $this->exchange->setType($type);
        $this->exchange->declareExchange();
        return $this;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $job     name ( exchange )
     * @param string $route   queue name ( route key )
     * @param mixed  $data    payload
     * @param array  $options delivery options
     *
     * @link(Set Delivery Mode, http://stackoverflow.com/questions/6882995/setting-delivery-mode-for-amqp-rabbitmq)
     * 
     * @throws AMQPException
     * @return boolean
     */
    public function push($job, $route, $data, $options = array())
    {
        $this->declareExchange($job);
        $queue = $this->declareQueue($route); // Get queue
        return $this->publishJob($queue, $job, $data, $options);
    }

    /**
     * Push a new job onto delayed queue.
     *
     * @param int    $delay   date
     * @param string $job     name
     * @param string $route   queue name ( Routing Key )
     * @param mixed  $data    payload
     * @param array  $options delivery options
     *
     * @link(Set Delivery Mode, http://stackoverflow.com/questions/6882995/setting-delivery-mode-for-amqp-rabbitmq)
     * 
     * @throws AMQPException
     * @return boolean
     */
    public function later($delay, $job, $route, $data, $options = array())
    {
        $this->declareExchange($job);
        $queue = $this->declareDelayedQueue($route, (int)$delay); // Get queue
        return $this->publishJob($queue, $job, $data, $options);
    }

    /**
     * Publish queue job
     * 
     * @param object $queue   AMQPQueue
     * @param string $job     queue name
     * @param array  $data    payload
     * @param array  $options delivery options
     * 
     * @return bool
     */
    protected function publishJob($queue, $job, $data, $options = array())
    {
        $options = empty($options) ? array(
            'delivery_mode' => 2,           // 2 = "Persistent", 1 = "Non-persistent"
            'content_type' => 'text/json'
        ) : $options;

        $payload = json_encode(array('job' => $job, 'data' => $data));
        $result = $this->exchange->publish(
            $payload, 
            $queue->getName(),
            AMQP_MANDATORY, 
            $options
        );
        if (! $result) {
            throw new AMQPException('Could not push job to a queue');
        }
        return $result;
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $job   exchange name
     * @param string $queue queue name ( routing key )
     *
     * @return mixed job handler object or null
     */
    public function pop($job, $queue = null)
    {
        $this->declareExchange($job);
        $AMQPqueue    = $this->declareQueue($queue); // Declare queue if not exists
        $AMQPenvelope = $AMQPqueue->get();  // Get envelope
    
        if ($AMQPenvelope instanceof AMQPEnvelope) { // * Send Message to JOB QUEUE
            return new AmqpJob($this, $AMQPqueue, $AMQPenvelope);  // Send incoming message to job class.
        }
        return null;
    }

    /**
     * Declare queue
     * 
     * @param string $name string
     *
     * @return object AMQPQueue
     */
    protected function declareQueue($name = null)
    {
        $name = (empty($name)) ? $this->defaultQueueName : $name;

        $queue = new AMQPQueue($this->channel);
        $queue->setName($name);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind($this->exchange->getName(), $name);
        $queue->declareQueue();
        return $queue;
    }

    /**
     * Declare delated queue
     * 
     * @param string  $destination delayed queue name
     * @param integer $delay       interval
     *
     * @return object AMQPQueue delayed object
     */
    protected function declareDelayedQueue($destination, $delay)
    {
        $destination = (empty($destination)) ? $this->defaultQueueName : $destination;
        $name = $destination . '_delayed_' . $delay;

        $queue = new AMQPQueue($this->channel);
        $queue->setName($name);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setArguments(
            [
                'x-dead-letter-exchange' => $this->exchange->getName(),
                'x-dead-letter-routing-key' => $destination,
                'x-message-ttl' => $delay * 1000,
                'x-expires' => $delay * 2000
            ]
        );
        $queue->declareQueue();
        $queue->bind($this->exchange->getName(), $name);
        $queue->declareQueue();
        return $queue;
    }

    /**
     * Delete a queue and its contents.
     *
     * @param string $queue queue name
     * 
     * @return object
     */
    public function delete($queue)
    {
        $channel = new AMQPChannel($this->AMQPconnection);
        $q = new AMQPQueue($channel);
        $q->setName($queue);
        $q->delete();
        return $this;
    }

}