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
     * @param array  $data payload
     * @param object $job  class \\Obullo\Queue\Job class
     * 
     * @return void
     */
    public function fire(array $data, $job = null);
}