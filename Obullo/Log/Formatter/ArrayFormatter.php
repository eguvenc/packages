<?php

namespace Obullo\Log\Formatter;

/**
 * Array formatter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ArrayFormatter
{
    /**
     * Format the line defined in providers/logger.php
     *
     * [%datetime%] %channel%.%level%: --> %message% %context% %extra%\n
     * 
     * @param array $unformattedRecord record data
     * @param array $params            logger service parameters
     * 
     * @return array
     */
    public static function format(array $unformattedRecord, array $params)
    {
        $time = (isset($unformattedRecord['datetime'])) ? $unformattedRecord['datetime'] : time();

        $record = array(
            'datetime' => date($params['format']['date'], $time),
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

}