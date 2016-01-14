<?php

namespace Obullo\Log\Filters;

use Obullo\Log\LoggerInterface as Logger;

/**
 * Priority filter
 * 
 * @copyright 2009-2016 Obullo
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
     * @param object $logger Logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set filter params
     * 
     * @param array $params array
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
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