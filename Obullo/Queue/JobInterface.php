<?php

namespace Obullo\Queue;

use Obullo\Container\ContainerInterface;

/**
 * Job Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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