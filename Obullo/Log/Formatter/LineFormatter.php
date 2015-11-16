<?php

namespace Obullo\Log\Formatter;

/**
 * Line formatter
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LineFormatter
{
    /**
     * Format the line defined in service/logger.php
     *
     * [%datetime%] %channel%.%level%: --> %message% %context% %extra%\n
     * 
     * @param array $record record data
     * @param array $params logger service parameters
     * 
     * @return array
     */
    public static function format(array $record, array $params)
    {
        if (! is_array($record)) {
            return;
        }
        return str_replace(
            array(
            '%datetime%',
            '%channel%',
            '%level%',
            '%message%',
            '%context%',
            '%extra%',
            ), 
            array(
            $record['datetime'],
            $record['channel'],
            $record['level'],
            $record['message'],
            (empty($record['context'])) ? '' : $record['context'],
            (empty($record['extra'])) ? '' : $record['extra'],
            $record['extra'],
            ),
            str_replace('\n', "\n", $params['format']['line'])
        );
    }

}