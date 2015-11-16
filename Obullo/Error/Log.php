<?php

namespace Obullo\Error;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Log helper for app/errors.php
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Log
{
    /**
     * Constructor
     * 
     * @param Logger $logger logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Write errors
     * 
     * @param Exception $e exception object
     * 
     * @return void
     */
    public function error(\Exception $e)
    {        
        if ($this->logger instanceof Logger) {

            $this->logger->channel('system');
            $this->logger->error(
                $e->getMessage(),
                [
                    'file' => Utils::securePath($e->getFile()),
                    'line' => $e->getLine()
                ]
            );
        }
    }
}

