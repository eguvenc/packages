<?php 

namespace Obullo\Queue\JobHandler;

use AMQPQueue;
use AMQPEnvelope;
use Obullo\Queue\Job;
use Obullo\Queue\QueueInterface;

/**
 * AMQPJob Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpJob extends Job
{
    /**
     * Queue
     * 
     * @var 
     */
    protected $queue;

    /**
     * AMQPQueue
     * 
     * @var object
     */
    protected $AMQPQueue;

    /**
     * AMQPEnvelope
     * 
     * @var object
     */
    protected $AMQPEnvelope;

    /**
     * Constructor
     * 
     * @param object $queue        \Obullo\Queue\QueueInterface
     * @param object $AMQPQueue    \AMQPQueue
     * @param object $AMQPEnvelope \AMQPEnvelope
     */
    public function __construct(QueueInterface $queue, AMQPQueue $AMQPQueue, AMQPEnvelope $AMQPEnvelope)
    {  
        $this->queue = $queue;
        $this->AMQPQueue = $AMQPQueue;
        $this->AMQPEnvelope = $AMQPEnvelope;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->AMQPEnvelope->getBody(), true));
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->AMQPEnvelope->getBody();
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
        $this->AMQPQueue->ack($this->AMQPEnvelope->getDeliveryTag());
    }

    /**
     * Get queue name
     *
     * @return string
     */
    public function getName()
    {
        return $this->AMQPQueue->getName();
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
        $this->delete(); // Delete the job

        $body = $this->AMQPEnvelope->getBody();
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
        $body = json_decode($this->AMQPEnvelope->getBody(), true);
        return isset($body['data']['attempts']) ? $body['data']['attempts'] : 0;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->AMQPEnvelope->getDeliveryTag();
    }
}