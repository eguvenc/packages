<?php

namespace Obullo\Log\Filter;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Log filter handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LogFilters
{
    /**
     * Handle log filters
     * 
     * @param array $event  current handler log event
     * @param array $logger logger
     * 
     * @return array single event data of writer
     */
    public static function handle(array $event, Logger $logger)
    {
        foreach ($event['filters'] as $value) {
            $Class = '\\'.$value['class'];
            $method = $value['method'];

            $filter = new $Class($value['params'], $logger); // Inject filter parameters
            if (count($event['record']) > 0) {
                $event['record'] = self::doFilter($event['record'], $filter, $method);
            }
        }
        return $event;
    }

    /**
     * Do filter for each record
     * 
     * @param array  $records data
     * @param object $filter  object
     * @param string $method  name
     * 
     * @return array records
     */
    public static function doFilter($records, $filter, $method)
    {
        $filteredRecords = array();
        foreach ($records as $key => $record) {
            $filteredRecord = $filter->$method($record);
            if (! empty($filteredRecord)) {
                $filteredRecords[$key] = $filteredRecord;
            }
        }
        return $filteredRecords;
    }
}