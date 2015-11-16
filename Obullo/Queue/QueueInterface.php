<?php

namespace Obullo\Queue;

/**
 * Queue Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface QueueInterface
{
    /**
     * Push a new job onto the queue.
     *
     * @param string $job     name
     * @param string $route   queue name ( route key )
     * @param mixed  $data    payload
     * @param array  $options delivery options
     *
     * @return boolean
     */
    public function push($job, $route, $data, $options = array());

    /**
     * Push a new job onto delayed queue.
     *
     * @param int    $delay   date
     * @param string $job     name
     * @param string $route   queue name ( Routing Key )
     * @param mixed  $data    payload
     * @param array  $options delivery options
     * 
     * @return boolean
     */
    public function later($delay, $job, $route, $data, $options = array());
    
    /**
     * Pop the next job off of the queue.
     *
     * @param string $job   exchange name
     * @param string $queue queue name ( routing key )
     *
     * @return mixed job handler object or null
     */
    public function pop($job, $queue = null);

    /**
     * Delete a queue
     *
     * @param string $queue queue name
     * 
     * @return object
     */
    public function delete($queue);
}