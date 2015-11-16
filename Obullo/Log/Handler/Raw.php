<?php

namespace Obullo\Log\Handler;

/**
 * Raw Handler 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Raw extends AbstractHandler implements HandlerInterface
{
    /**
     * Service configuration
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param array $params logger service parameters
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Write output
     *
     * @param string $event current handler log event
     * 
     * @return mixed
     */
    public function write(array $event)
    {
        $lines = '';
        foreach ($event['record'] as $record) {
            $record = $this->arrayFormat($event, $record);
            $lines .= $this->lineFormat($record);
        }
        return $lines;
    }

    /**
     * Close handler connection
     * 
     * @return void
     */
    public function close()
    {
        return;
    }
}