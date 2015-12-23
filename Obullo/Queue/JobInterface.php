<?php

namespace Obullo\Queue;

use Obullo\Container\ContainerInterface;

/**
 * Job Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface JobInterface
{
    /**
     * Fire the job
     * 
     * @param object $job  class \\Obullo\Queue\Job class
     * @param array  $data payload
     * 
     * @return void
     */
    public function fire($job, array $data);
}