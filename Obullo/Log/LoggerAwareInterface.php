<?php

namespace Obullo\Log;

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
