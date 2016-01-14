<?php

namespace Obullo\Log;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Log filter handler
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Filter
{
    /**
     * Handle log filters
     * 
     * @param array $event current handler log event
     * 
     * @return array single event data of writer
     */
    public static function handle(array $event)
    {
        global $c;
        foreach ($event['filters'] as $value) {
            $Class = '\\'.$value['class'];
            $method = $value['method'];

            $filter = $c['dependency']->resolveDependencies($Class);  // Resolve components

            if (method_exists($filter, 'setParams')) {
                $filter->setParams($value['params']); // Inject filter parameters
            }
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