<?php

namespace Obullo\Log;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Container\ParamsAwareInterface;
use League\Container\ImmutableContainerAwareInterface;

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

            if ($filter instanceof ImmutableContainerAwareInterface) {
                global $container;
                $filter->setContainer($container);
            }
            if ($filter instanceof ParamsAwareInterface) {
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