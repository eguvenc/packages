<?php

namespace Obullo\Log\Handler;

use Closure;

/**
 * Email Handler 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Email extends AbstractHandler implements HandlerInterface
{
    /**
     * Mail message
     * 
     * @var string
     */
    protected $message;

    /**
     * Closure function
     * 
     * @var object
     */
    protected $closure;

    /**
     * Newline character
     * 
     * @var string
     */
    protected $newlineChar = '<br />';

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
     * Sets your custom newline character
     * 
     * @param string $newline char
     *
     * @return void
     */
    public function setNewlineChar($newline = '<br />')
    {
        $this->newlineChar = $newline;
    }

    /**
     * Set mailer message
     * 
     * @param string $message message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = (string)$message;
    }

    /**
     * Sets closure function for send method
     * 
     * @param Closure $closure closure
     * 
     * @return void
     */
    public function func(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Writer 
     *
     * @param array $event current handler event
     * 
     * @return void
     */
    public function write(array $event)
    {
        $lines = '';
        foreach ($event['record'] as $record) {
            $record = $this->arrayFormat($event, $record);
            $lines .= str_replace("\n", $this->newlineChar, $this->lineFormat($record));
        }
        $message = sprintf($this->message, $lines);
        $closure = $this->closure;

        if (is_callable($closure)) {  // Send formatted message
            return $closure($message);
        }
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