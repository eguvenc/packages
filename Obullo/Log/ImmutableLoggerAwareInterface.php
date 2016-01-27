<?php

namespace Obullo\Container;

use Psr\Log\LoggerInterface as Logger;

/**
 * Inject parameters
 */
interface ImmutableLoggerAwareInterface
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
