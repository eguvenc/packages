<?php

namespace Obullo\Container;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Inject parameters
 */
interface LoggerAwareInterface
{
    /**
     * Set params
     *
     * @param object $logger Obullo\Log\LoggerInterface
     * 
     * @return $this
     */
    public function setLogger(Logger $logger);

    /**
     * Get params
     *
     * @return array
     */
    public function getLogger();
}
