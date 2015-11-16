<?php

namespace Obullo\Log\Handler;

/**
 * Syslog Handler 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Syslog extends AbstractHandler implements HandlerInterface
{
    /**
     * Facility used by this syslog instance
     * 
     * @var string
     */
    public $facility = LOG_USER;

    /**
     * Syslog application name
     * 
     * @var string
     */
    public $name = 'LogHandler.Syslog';

    /**
     * Service configuration
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param array $params service parameters
     * @param array $config handler config
     */
    public function __construct(array $params, $config = array())
    {
        $this->params = $params;

        if (! empty($config['facility'])) {
            $this->facility = $config['facility'];  // Application facility
        }
        if (! empty($config['name'])) {       // Application name
            $this->name = $config['name'];
        }
        openlog($this->name, LOG_PID, $this->facility);
    }

    /**
     * Write output
     *
     * @param string $event current handler log event
     * 
     * @return void
     */
    public function write(array $event)
    {
        foreach ($event['record'] as $record) {
            $record = $this->arrayFormat($event, $record);
            syslog($record['level'], $this->lineFormat($record));
        }
    }

    /**
     * Close handler connection
     * 
     * @return void
     */
    public function close() 
    {
        closelog();
    }
}