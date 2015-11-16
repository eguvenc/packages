<?php

namespace Obullo\Queue\Handler;

use RuntimeException;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;

use Obullo\Queue\QueueInterface;
use Obullo\Config\ConfigInterface;
use Obullo\Queue\JobHandler\AmqpLibJob;
use Obullo\Container\ServiceProviderInterface;

/**
 * For COMPOSER package videlalvaro/php-amqplib
 */

/**
 * AmqpLib Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpLib implements QueueInterface
{
    /**
     * AMQP channel
     * 
     * @var string
     */
    protected $ch = null;

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
        
        $this->ch = $this->AMQPconnection->channel();
        $this->defaultQueueName = 'default';
    }

    /**
     * Parse amqp options and returns to amqp supported types
     * 
     * @param string $type available types (direct) AMQP_EX_TYPE_DIRECT, (fanout) AMQP_EX_TYPE_FANOUT
     * @param string $flag available flags AMQP_DURABLE, AMQP_PASSIVE
     * 
     * @return array
     */
    protected function getOptions($type = null, $flag = null)
    {
        $type = (empty($type)) ? strtolower($this->config['exchange']['type']) : strtolower($type);
        $flag = (empty($flag)) ? strtolower($this->config['exchange']['flag']) : strtolower($flag);
        
        $passive = true;
        $durable = false;
        if ($flag == 'durable') {
            $passive = false;
            $durable = true;
        }
        return [
            'type' => $type,
            'passive' => $passive,
            'durable' => $durable
        ];
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $job     name (exchange)
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
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $exchange = $job;
        $queue  = (empty($route)) ? $this->defaultQueueName : $route;
        $params = $this->getOptions();

        $this->ch->queue_declare($queue, false, true, false, false);
        $this->ch->exchange_declare($exchange, $params['type'], $params['passive'], $params['durable'], false);
        $this->ch->queue_bind($queue, $exchange);

        return $this->publishJob($exchange, $data, null, $options);
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
     * @link(SetDeliveryMode, http://stackoverflow.com/questions/6882995/setting-delivery-mode-for-amqp-rabbitmq)
     * @link(PhpAmqpDelay,    http://stackoverflow.com/questions/21942063/how-to-delay-php-amqplib?rq=1)
     * 
     * @throws AMQPException
     * @return boolean
     */
    public function later($delay, $job, $route, $data, $options = array())
    {
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $exchange = $job;
        $destination = (empty($route)) ? $this->defaultQueueName : $route;
        $queueDelayed = $destination . '_delayed_' . $delay;
        $exchangeDelayed = $job . '_delayed_' . $delay;

        $arguments = new AMQPTable(
            [
                'x-dead-letter-exchange' => $exchange,
                'x-message-ttl'          => $delay * 1000,
                'x-expires'              => $delay * 2000
            ]
        );
        $this->ch->queue_declare($destination, false, true, false, false, false);
        $this->ch->exchange_declare($exchange, 'direct', false, true, false);
        $this->ch->queue_bind($destination, $job);

        $this->ch->queue_declare($queueDelayed, false, true, false, false, false, $arguments);
        $this->ch->exchange_declare($exchangeDelayed, 'direct', false, true, false);
        $this->ch->queue_bind($queueDelayed, $exchangeDelayed);

        return $this->publishJob($exchange, $data, $exchangeDelayed, $options);
    }

    /**
     * Publish queue job
     * 
     * @param string $exchange        job
     * @param array  $data            payload
     * @param mixed  $exchangeDelayed delayed exchange
     * @param array  $options         delivery options
     * 
     * @return bool
     */
    protected function publishJob($exchange, $data, $exchangeDelayed = null, $options = array())
    {
        $options = empty($options) ? array(
            'delivery_mode' => 2,           // 2 = "Persistent", 1 = "Non-persistent"
            'content_type' => 'text/json'
        ) : $options;

        $message = new AMQPMessage(json_encode(['job' => $exchange, 'data' => $data]), $options);
        
        $job = ($exchangeDelayed == null) ? $exchange : $exchangeDelayed;
        $this->ch->basic_publish($message, $job);
        $this->ch->close();
        return true;
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
        $exchange = $job;
        $queueName = (empty($queue)) ? $this->defaultQueueName : $queue;
        $params = $this->getOptions();

        $this->ch->queue_declare($queueName, false, true, false, false);
        $this->ch->exchange_declare($exchange, $params['type'], $params['passive'], $params['durable'], false);
        $this->ch->queue_bind($queueName, $exchange);

        $AMQPMessage = $this->ch->basic_get($queueName);
        $AMQPMessage->delivery_info['routing_key'] = $queueName;  // Fill routing key it comes empty.

        if ($AMQPMessage instanceof AMQPMessage) {     // * Send Message to JOB QUEUE
            return new AmqpLibJob($this, $this->ch, $AMQPMessage);  // Send incoming message to job class.
        }
        return null;
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
        $channel = $this->AMQPconnection->channel();
        $channel->queue_delete($queue);
        return $this;
    }

}