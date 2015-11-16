<?php

namespace Obullo\Queue\JobHandler;

use Obullo\Queue\Job;
use Obullo\Queue\QueueInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

/**
 * AMQPLibJob Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpLibJob extends Job
{
    /**
     * Queue
     * 
     * @var object
     */
    protected $queue;

    /**
     * AMQPChannel
     * 
     * @var object
     */
    protected $AMQPChannel;

    /**
     * AMQMessage
     * 
     * @var object
     */
    protected $AMQPMessage;

    /**
     * Constructor
     * 
     * @param object $queue   QueueInterface \Obullo\Queue\QueueInterface
     * @param object $channel AMQPChannel    \PhpAmqpLib\Channel\AMQPChannel
     * @param object $message AMQPMessage    \PhpAmqpLib\Message\AMQPMessage
     */
    public function __construct(QueueInterface $queue, AMQPChannel $channel, AMQPMessage $message)
    {  
        $this->queue = $queue;
        $this->AMQPChannel = $channel;
        $this->AMQPMessage = $message;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->AMQPMessage->body, true));
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->AMQPMessage->body;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
        $this->AMQPChannel->basic_ack($this->AMQPMessage->delivery_info['delivery_tag']);
    }

    /**
     * Get queue name
     *
     * @return string
     */
    public function getName()
    {  
        return $this->AMQPMessage->delivery_info['routing_key'];
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay interval
     *
     * @return void
     */
    public function release($delay = 0)
    {
        $this->delete();  // Delete the job.

        $body = $this->AMQPMessage->body;
        $body = json_decode($body, true);
        $body['data']['attempts'] = $this->getAttempts() + 1; // Write attempts to body

        if ($delay > 0) {
            $this->queue->later($delay, $body['job'], $this->getName(), $body['data']);
        } else {
            $this->queue->push($body['job'], $this->getName(), $body['data']);
        }
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function getAttempts()
    {
        $body = json_decode($this->AMQPMessage->body, true);
        return isset($body['data']['attempts']) ? $body['data']['attempts'] : 0;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->AMQPMessage->delivery_info['delivery_tag'];
    }
}