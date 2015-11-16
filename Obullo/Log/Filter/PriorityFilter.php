<?php

namespace Obullo\Log\Filter;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Priority filter
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PriorityFilter
{
    /**
     * Logger class
     * 
     * @var object
     */
    protected $logger;

    /**
     * Parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param array  $params filter parameters
     * @param object $logger Logger
     */
    public function __construct(array $params, Logger $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
    }

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
        $priority = $this->logger->getPriority($record['level']);

        if (in_array($priority, $this->params)) {
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
        $priority = $this->logger->getPriority($record['level']);

        if (! in_array($priority, $this->params)) {
            return $record;
        }
        return;
    }
}