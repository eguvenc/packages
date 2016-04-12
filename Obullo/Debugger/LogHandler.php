<?php

namespace Obullo\Debugger;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Log handler
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LogHandler extends AbstractProcessingHandler
{
    /**
     * Payload
     * 
     * @var array
     */
    protected $payload = array();

    /**
     * Constructor
     * 
     * @param int     $level  level
     * @param boolean $bubble bubble
     */
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * Write record into array
     * 
     * @param array $record record
     * 
     * @return void
     */
    protected function write(array $record)
    {
        $this->payload[] = array(
            'channel' => $record['channel'],
            'level_name' => $record['level_name'],
            'message' => $record['message'],
            'datetime' => $record['datetime']->format('U'),
            'context' => $record['context'],
            'extra' => $record['extra'],
        );
    }

    /**
     * Returns to log records.
     * 
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
}