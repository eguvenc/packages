<?php 

namespace Obullo\Queue;

use DateTime;

/**
 * Job Class - This modeled after Laravel job class 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class Job
{
    /**
     * Queue instance
     * 
     * @var object
     */
    protected $queue;

    /**
     * The job handler instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
     */
    protected $queueName;

    /**
     * Indicates if the job has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Worker Environment
     * 
     * @var string
     */
    public $env;

    /**
     * Fire the job.
     *
     * @return void
     */
    abstract public function fire();

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * Determine if the job has been deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay interval
     * 
     * @return void
     */
    abstract public function release($delay = 0);

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    abstract public function getAttempts();

    /**
     * Get the job id
     * 
     * @return int
     */
    abstract public function getId();

    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    abstract public function getRawBody();

    /**
     * Resolve and fire the job handler method.
     *
     * @param array $payload data
     * 
     * @return void
     */
    protected function resolveAndFire(array $payload)
    {
        global $c;
        $Class = str_replace('@', '\\', ucfirst($payload['job']));
        $this->instance = new $Class($c, array('env' => $this->getEnv()));
        $this->instance->fire($this, $payload['data']);
    }

    /**
     * Calculate the number of seconds with the given delay.
     *
     * @param mixed $delay datetime or int
     * 
     * @return int
     */
    protected function getSeconds($delay)
    {
        if ($delay instanceof DateTime) {
            return max(0, $delay->getTimestamp() - time());
        } else {
            return intval($delay);
        }
    }

    /**
     * Set environment of current worker
     *
     * @param string $env environment
     * 
     * @return string
     */
    public function setEnv($env = 'local')
    {
        $this->env = $env;
    }

    /**
     * Get environment of current worker
     * 
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

}