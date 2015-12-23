<?php

namespace Obullo\Log\Formatter;

/**
 * Debug formatter for http debugger
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class DebugFormatter
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
        $format = str_replace('\n', "", $params['format']['line']);
        $formatted = preg_replace('#([^\s\w:\->%.]+)#', '', $format);

        $search = [
            '%datetime%',
            '%channel%',
            '%level%',
            '%message%',
            '%context%',
            '%extra%',
        ];
        $class = $record['level'];
        if (preg_match('#Request Uri\b#', $record['message'])) {
            $class = 'debug title';
        }
        $replace = [
            '<div class="p '.$class.'"><span class="date">'.$record['datetime'].'</span>',
            $record['channel'],
            $record['level'],
            $record['message'],
            (empty($record['context'])) ? '' : $record['context'],
            (empty($record['extra'])) ? '' : $record['extra'],
        ];
        return str_replace($search, $replace, $formatted)."</div>\n";
    }

}