<?php

namespace Obullo\Log\Filters;

use Obullo\Container\ParamsAwareTrait;
use Obullo\Container\ParamsAwareInterface;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Priority filter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PriorityFilter implements ImmutableContainerAwareInterface, ParamsAwareInterface
{
    use ImmutableContainerAwareTrait, ParamsAwareTrait;

    /**
     * Filter in array
     * 
     * @param array $record unformatted record data
     * 
     * @return array|null
     */
    public function in(array $record)
    {
        if (empty($record)) {
            return array();
        }
        $priority = $this->getContainer()
            ->get('logger')
            ->getPriority($record['level']);

        if (in_array($priority, $this->getParams())) {
            return $record;
        }
        return;
    }

    /**
     * Filter "not" in array
     * 
     * @param array $record unformatted record data
     * 
     * @return array|null
     */
    public function notIn(array $record)
    {
        if (empty($record)) {
            return array();
        }
        $priority = $this->getContainer()
            ->get('logger')
            ->getPriority($record['level']);

        if (! in_array($priority, $this->getParams())) {
            return $record;
        }
        return;
    }
}