<?php

namespace Obullo\Log\Handler;

use Obullo\Log\Formatter\LineFormatter;
use Obullo\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Abstract Log Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractHandler
{
    /**
     * Check log writing is allowed, deny not allowed
     * requests.
     *
     * @param array                                   $event   handler log event
     * @param Psr\Http\Message\ServerRequestInterface $request request
     * 
     * @return boolean
     */
    public function isAllowed(array $event, $request)
    {
        $isBrowserRequest = ($event['request'] == 'http' || $event['request'] == 'ajax') ? true : false;

        if (in_array($event['request'], array(null, 'http','ajax','cli'))) { // Disable logs if request not allowed
            if ($isBrowserRequest && $request->getUri()->segment(0) == 'debugger') {  // Disable http debugger logs
                return false;
            }
            return true;
        }
        if ($event['request'] == 'worker') {
            return $this->params['app']['worker']['log'];    // Disable / enable worker logs
        }
        return false;
    }

    /**
    * Format log records
    *
    * @param string $event             all log data
    * @param array  $unformattedRecord current log record
    * 
    * @return array formatted record
    */
    public function arrayFormat(array $event, array $unformattedRecord)
    {
        $record = array(
            'datetime' => date($this->params['format']['date'], $event['time']),
            'channel'  => $unformattedRecord['channel'],
            'level'    => $unformattedRecord['level'],
            'message'  => $unformattedRecord['message'],
            'context'  => null,
            'extra'    => null,
        );
        if (isset($unformattedRecord['context']['extra']) && count($unformattedRecord['context']['extra']) > 0) {
            $record['extra'] = var_export($unformattedRecord['context']['extra'], true);
            unset($unformattedRecord['context']['extra']);     
        }
        if (count($unformattedRecord['context']) > 0) {
            $str = var_export($unformattedRecord['context'], true);
            $record['context'] = strtr($str, array("\r\n" => '', "\r" => '', "\n" => ''));
        }
        return $record; // formatted record
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