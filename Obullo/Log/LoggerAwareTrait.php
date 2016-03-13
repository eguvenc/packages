<?php

namespace Obullo\Container;

use Obullo\Log\LoggerInterface as Logger;

trait LoggerAwareTrait
{
    /**
     * Logger
     * 
     * @var array
     */
    protected $logger;

    /**
     * Set params
     *
     * @param object $logger Obullo\Log\LoggerInterface
     * 
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
