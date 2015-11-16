<?php

namespace Obullo\Log\Formatter;

/**
 * Debug formatter for http debugger
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
        $format = preg_replace('#([^\s\w:\->%.]+)#', '', $format);

        $search = [
            '%datetime%',
            '%channel%',
            '%level%',
            '%message%',
            '%context%',
            '%extra%',
        ];
        $replace = [
            '<div class="p"><span class="date">'.$record['datetime'].'</span>',
            $record['channel'],
            $record['level'],
            $record['message'],
            (empty($record['context'])) ? '' : $record['context'],
            (empty($record['extra'])) ? '' : $record['extra'],
        ];
        $line = str_replace($search, $replace, $format)."</div>\n";

        $levelPatterns = array(
            '#<div class="p">(.*(Request Uri\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.error\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.warning\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.notice\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.emergency\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.critical\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.alert\b).*)<\/div>#',
            '#<div class="p">(.*(.*\.info\b).*)<\/div>#',
        );
        $levelReplace = array(
            '<div class="p title">$1</div>',
            '<div class="p error">$1</div>',
            '<div class="p error">$1</div>',
            '<div class="p error">$1</div>',
            '<div class="p error">$1</div>',
            '<div class="p error">$1</div>',
            '<div class="p alert">$1</div>',
            '<div class="p info">$1</div>',
        );
        return preg_replace($levelPatterns, $levelReplace, $line);
    }

}