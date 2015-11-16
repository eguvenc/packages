<?php

namespace Obullo\Log;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Disable Logger Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class NullLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * Load defined log handler
     * 
     * @param string $name defined log handler name
     * 
     * @return object
     */
    public function load($name)
    {
        $name = null;
        return $this;
    }

    /**
     * Set priority value for current handler 
     * or writer.
     * 
     * @param integer $priority level
     * 
     * @return object
     */
    public function priority($priority = 0)
    {
        $priority = null;
        return $this;
    }

    /**
     * Change channel
     * 
     * @param string $channel add a channel
     * 
     * @return object
     */
    public function channel($channel)
    {
        $channel = null;
        return $this;
    }

    /**
     * Reserve your filter to valid log handler
     * 
     * @param string $name   filter name
     * @param array  $params data
     * 
     * @return object
     */
    public function filter($name, $params = array())
    {
        $name = null;
        $params = array();
        return $this;
    }

    /**
     * Push to another handler
     * 
     * @return void
     */
    public function push()
    {
        return $this;
    }

    /**
     * If logger disabled all logger methods returns to null.
     * 
     * @param string  $level    log level
     * @param string  $message  log message
     * @param array   $context  context data
     * @param integer $priority message priority
     * 
     * @return void
     */
    public function log($level, $message, $context = array(), $priority = null)
    {
        return $level = $message = $context = $priority = null;
    }

    /**
     * Add writer
     * 
     * @param string $name handler key
     * @param string $type writer/handler
     *
     * @return object
     */
    public function setWriter($name, $type = 'writer')
    {
        $name = $type = null;
        return $this;
    }

    /**
     * Returns to primary writer name.
     * 
     * @return string returns to "handler" e.g. "file"
     */
    public function getWriter()
    {
        return 'null';
    }

    /**
     * Enable html debugger
     * 
     * @return void
     */
    public function printDebugger()
    {
        return;
    }

    /**
     * Returns to rendered log records
     * 
     * @return array
     */
    public function getPayload()
    {
        return array();
    }

    /**
     * End of the logs and beginning of the handlers.
     * 
     * @param ResponseInterface $response http response
     *  
     * @return void
     */
    public function shutdown(Response $response = null)
    {
        return $response;
    }

}