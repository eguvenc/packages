<?php

namespace Obullo\Log;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Container\ContainerAwareInterface;

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
        if (empty($event['filters'])) {
            return array();
        }
        foreach ($event['filters'] as $value) {
            $Class = '\\'.$value['class'];
            $method = $value['method'];

            $filter = new $Class;  // Resolve components
            if ($filter instanceof ContainerAwareInterface) {
                global $container;
                $filter->setContainer($container);
            }
            if (count($event['records']) > 0) {
                $event['records'] = self::doFilter($event['records'], $filter, $method, $value['params']);
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
     * @param array  $params  filter options
     * 
     * @return array records
     */
    public static function doFilter($records, $filter, $method, $params = array())
    {
        $filteredRecords = array();
        foreach ($records as $key => $record) {
            $filteredRecord = $filter->$method($record, $params);
            if (! empty($filteredRecord)) {
                $filteredRecords[$key] = $filteredRecord;
            }
        }
        return $filteredRecords;
    }
}