<?php

namespace Obullo\Debugger;

/**
 * Log formatter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LogFormatter
{
    /**
     * Format the line
     *
     * [%datetime%] %channel%.%level%: %message% %context% %extra%\n
     * 
     * @param array $record record data
     * 
     * @return array
     */
    public static function getLine(array $record)
    {
        if (! is_array($record)) {
            return;
        }
        $format    = str_replace('\n', "", '[%datetime%] %channel%.%level%: %message% %context% %extra%\n');
        $formatted = preg_replace('#([^\s\w:\ %.]+)#', '', $format);  // Remove "[" "]" brackets

        $search = [
            '%datetime%',
            '%channel%',
            '%level%',
            '%message%',
            '%context%',
            '%extra%',
        ];
        $class = $record['level_name'];
        if (preg_match('#Request Uri\b#', $record['message'])) {
            $class = 'debug title';
        }
        $replace = [
            '<div class="p '.strtolower($class).'"><span class="date">'.date('Y-m-d H:i:d', $record['datetime']).'</span>',
            $record['channel'],
            $record['level_name'],
            $record['message'],
            (empty($record['context'])) ? '' : var_export($record['context'], true),
            (empty($record['extra'])) ? '' : var_export($record['extra'], true),
        ];

        return str_replace($search, $replace, $formatted)."</div>\n";
    }

    /**
     * Write output
     *
     * @param array $records data
     * 
     * @return mixed
     */
    public function format(array $records)
    {
        $lines = '';
        foreach ($records as $record) {
            $lines.= self::getLine($record);
        }
        return $lines;
    }
}