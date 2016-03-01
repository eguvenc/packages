<?php

namespace Obullo\Log\Handler;

use Obullo\Log\Formatter\LineFormatter;
use Obullo\Log\Formatter\ArrayFormatter;

/**
 * Abstract Log Handler
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractHandler
{
    /**
    * Format log records
    *
    * @param array $unformattedRecord current log record
    * 
    * @return array formatted record
    */
    public function arrayFormat(array $unformattedRecord)
    {
        return ArrayFormatter::format($unformattedRecord, $this->params);
    }

    /**
     * Format the line defined in config/env.$env/config.php
     * 
     * @param array $record one log data
     * 
     * @return string
     */
    public function lineFormat(array $record)
    {
        return LineFormatter::format($record, $this->params);
    }

    /**
     * Write log data
     *
     * @param array $event all log data
     * 
     * @return boolean
     */
    abstract public function write(array $event);

    /**
     * Close connection
     * 
     * @return void
     */
    abstract public function close();

}