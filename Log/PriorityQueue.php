<?php

namespace Obullo\Log;

use SplPriorityQueue;

/**
 * PriorityQueue Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PriorityQueue extends SplPriorityQueue 
{
    /**
     * Queue order
     * 
     * @var integer
     */
    protected $serial = PHP_INT_MAX;

    /**
     * Add to queue
     * 
     * @param string $value    data
     * @param mixed  $priority priority
     * 
     * @return void
     */
    public function insert($value, $priority)
    {
        if (is_null($priority)) {  // If we you use negative numbers null values will be smaller than negative numbers.
            $priority = 0;         // By converting null values to "0" we fix the problem.
        }                          
        parent::insert($value, array($priority, $this->serial--));  // http://php.net/manual/tr/splpriorityqueue.compare.php
    }
}