<?php

namespace Obullo\Log\Handler;

/**
 * Syslog Handler 
 * 
 * @copyright 2009-2016 Obullo
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
     * @param array $params  service parameters
     * @param array $options handler options
     */
    public function __construct(array $params, $options = array())
    {
        $this->params = $params;

        if (! empty($options['facility'])) {
            $this->facility = $options['facility'];  // Application facility
        }
        if (! empty($options['name'])) {       // Application name
            $this->name = $options['name'];
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
        foreach ($event['records'] as $record) {
            $record = $this->arrayFormat($record);
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