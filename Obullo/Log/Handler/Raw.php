<?php

namespace Obullo\Log\Handler;

/**
 * Raw Handler 
 * 
 * @copyright 2009-2016 Obullo
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
        foreach ($event['records'] as $record) {
            $record = $this->arrayFormat($record);
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