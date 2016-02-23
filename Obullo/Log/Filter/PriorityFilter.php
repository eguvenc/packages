<?php

namespace Obullo\Log\Filter;

use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Priority filter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PriorityFilter implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Filter in array
     * 
     * @param array $record log record
     * @param array $levels log levels
     * 
     * @return array|null
     */
    public function in(array $record, $levels = array())
    {
        if (empty($record)) {
            return array();
        }
        $priority = $this->getContainer()
            ->get('logger')
            ->getPriority($record['level']);

        if (in_array($priority, $levels)) {
            return $record;
        }
        return;
    }

    /**
     * Filter "not" in array
     * 
     * @param array $record log record
     * @param array $levels log levels
     * 
     * @return array|null
     */
    public function notIn(array $record, $levels = array())
    {
        if (empty($record)) {
            return array();
        }
        $priority = $this->getContainer()
            ->get('logger')
            ->getPriority($record['level']);

        if (! in_array($priority, $levels)) {
            return $record;
        }
        return;
    }
}