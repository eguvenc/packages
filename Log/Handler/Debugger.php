<?php

namespace Obullo\Log\Handler;

use Obullo\Log\Formatter\DebugFormatter;

/**
 * Http Debugger Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Debugger extends AbstractHandler implements HandlerInterface
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
     * @param string $event single record data
     * 
     * @return mixed
     */
    public function write(array $event)
    {
        $lines = '';
        foreach ($event['record'] as $record) {
            $record = $this->arrayFormat($event, $record);
            $lines.= DebugFormatter::format($record, $this->params);
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