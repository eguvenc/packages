<?php

namespace Obullo\Log;

use Closure;
use Exception;
use LogicException;
use ErrorException;
use RuntimeException;
use Obullo\Queue\Queue;
use Obullo\Error\ErrorHandler;
use Obullo\Container\ContainerInterface as Container;

/**
 * Logger Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Logger extends AbstractLogger implements LoggerInterface
{
    protected $c;                             // Container
    protected $p = 100;                       // Default priority
    protected $params = array();              // Service parameters
    protected $channel = 'system';            // Default log channel
    protected $writer;                        // Default writer
    protected $shutdown = false;              // Shutdown switch
    protected $handlers = array();            // Handlers
    protected $hcount = 0;                    // Handler count
    protected $hrcount = 0;                   // Handler record count
    protected $connect = false;               // Lazy connections
    protected $push = array();                // Push data
    protected $payload = array();             // Payload
    protected $priorityQueue = array();       // Log priority queue objects
    protected $handlerRecords = array();      // Handler records
    protected $loadedHandlers = array();      // Loaded handlers
    protected $requestString;

    /**
     * Registered error handlers
     *
     * @var bool
     */
    protected static $registeredErrorHandler = false;
    protected static $registeredExceptionHandler = false;
    protected static $registeredFatalErrorShutdownFunction = false;

    /**
     * Constructor
     *
     * @param object $c      container
     * @param array  $params parameters
     */
    public function __construct(Container $c, $params = array())
    {
        $this->c = $c;
        $this->params  = $params;
        $this->enabled = $c['config']['log']['enabled'];
        
        $this->initialize();
    }

    /**
     * Initialize config parameters
     * 
     * @return void
     */
    public function initialize()
    {
        $this->channel = $this->params['default']['channel'];
        $this->detectRequest($this->c['request']);
    }

    /**
     * Lazy connections
     * 
     * We execute this method in LoggerTrait sendQueue() method 
     * then if connect == true we open the connection in close method.
     *
     * @param boolean $connect on / off connection
     * 
     * @return void
     */
    public function connect($connect = true)
    {
        $this->connect = $connect;
    }
    
    /**
     * Whether to learn we have log data
     * 
     * @return boolean
     */
    protected function isConnected()
    {
        return $this->connect;
    }

    /**
     * Load defined log handler
     * 
     * @param string $name defined log handler name
     * 
     * @return object
     */
    public function load($name)
    {
        if (! isset($this->registeredHandlers[$name])) {
            throw new LogicException(
                sprintf(
                    'The push handler %s is not defined in your logger service.', 
                    $name
                )
            );
        }
        $this->addHandler($name);
        return $this;
    }

    /**
     * Add push handler
     * 
     * @param string $name name
     *
     * @return object Logger
     */
    protected function addHandler($name) 
    {
        $this->handlers[$name] = array('priority' => $this->registeredHandlers[$name]['priority']);
        $this->loadedHandlers[$this->hcount] = $name;
        $this->priorityQueue['handler.'.$name] = new PriorityQueue;
        $this->track[] = array('name' => $name);
        ++$this->hcount;
        return $this;
    }

    /**
     * Add writer
     * 
     * @param string $name handler key
     *
     * @return object
     */
    public function setWriter($name)
    {
        $this->priorityQueue[$name] = new PriorityQueue;
        $this->writer = $name;
        $this->track[] = array('name' => $name);
        return $this;
    }

    /**
     * Returns to primary writer name.
     * 
     * @return string returns to "xWriter"
     */
    public function getWriter()
    {
        return $this->writer;
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
        $this->channel = $channel;
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
        $method = 'filter';
        if (strpos($name, '@') > 0) {
            list($name, $method) = explode('@', $name);
        }
        if (! isset($this->filterNames[$name])) {
            throw new LogicException(
                sprintf(
                    'The filter %s is not registered in your logger service. Please first register it with following command. <pre>%s</pre> .', 
                    $name,
                    '$logger->registerFilter(\'filtername\', \'Log\Filters\ClassNameFilter\');'
                )
            );
        }
        $end = end($this->track);
        $handler = $end['name'];
        $this->filters[$handler][] = array('class' => $this->filterNames[$name], 'method' => $method, 'params' => $params);
        return $this;
    }

    /**
     * Store log data into array
     * 
     * @param string  $level    log level
     * @param string  $message  log message
     * @param array   $context  context data
     * @param integer $priority message priority
     * 
     * @return object Logger
     */
    public function log($level, $message, $context = array(), $priority = null)
    {
        if (! $this->isEnabled()) {
            return $this;
        }

        if (is_object($message) && $message instanceof Exception) {
            $this->logExceptionError($message);
            return $this;
        }
        $priority = $this->getPriority($level, $priority);

        if (count($this->loadedHandlers) > 0) {   // Start log capture and reset capture when we use push() method.
            $this->handlerRecords[$this->hrcount]['channel'] = $this->channel;
            $this->handlerRecords[$this->hrcount]['level']   = $level;
            $this->handlerRecords[$this->hrcount]['message'] = $message;
            $this->handlerRecords[$this->hrcount]['context'] = $context;
            $this->handlerRecords[$this->hrcount]['priority'] = $priority;
            ++$this->hrcount;
            return $this;
        }
        $recordUnformatted = array();
        $recordUnformatted['channel'] = $this->channel;
        $recordUnformatted['level']   = $level;
        $recordUnformatted['message'] = $message;
        $recordUnformatted['context'] = $context;
        $this->sendToWriterQueue($recordUnformatted, $priority);    // Send to Job queue
        $this->channel($this->params['default']['channel']);        // reset channel to default
        return $this;
    }

    /**
     * Get priority of log if priority not 
     * 
     * @param string  $level    severity
     * @param integer $priority priority
     * 
     * @return integer
     */
    public function getPriority($level, $priority = null)
    {
        if (empty($priority)) {
            $priorities = $this->getPriorities();
            if (isset($priorities[$level])) {
                return $priorities[$level];
            }
        }
        return $priority;
    }

    /**
     * Send logs to Queue for each log handler.
     *
     * $processor = new SplPriorityQueue;
     * $processor->insert($record, $priority = 0); 
     * 
     * @param array   $recordUnformatted unformated log data
     * @param integer $priority          priority
     * 
     * @return void
     */
    protected function sendToWriterQueue($recordUnformatted, $priority = null)
    {
        if (! empty($recordUnformatted)) {
            $this->connect(true);
            $this->priorityQueue[$this->writer]->insert($recordUnformatted, $priority);
        }
    }

    /**
     * Send handler's log to Queue
     * 
     * @param string  $name              handler
     * @param array   $recordUnformatted records 
     * @param integer $priority  
     * 
     * @return void
     */
    protected function sendToHandlerQueue($name, $recordUnformatted, $priority = null)
    {
        if (! empty($recordUnformatted)) {
            $this->connect(true);
            $this->priorityQueue['handler.'.$name]->insert($recordUnformatted, $priority);
        }
    }

    /**
     * Get splPriority object of valid handler
     * 
     * @param string $handler name
     * @param string $prefix  name
     * 
     * @return object of handler
     */
    protected function getQueue($handler = 'file', $prefix = '')
    {
        if (! isset($this->priorityQueue[$prefix.$handler])) {
            throw new LogicException(
                sprintf(
                    'The log handler %s is not defined.', 
                    $handler
                )
            );
        }
        return $this->priorityQueue[$prefix.$handler];
    }

    /**
     * Returns to filters of handler
     * 
     * @param string $handler name
     * 
     * @return array filters
     */
    public function getFilters($handler)
    {
        return $this->filters[$handler];
    }

    /**
     * Log exceptional messages
     * 
     * @param object $e ErrorException
     * 
     * @return void
     */
    public function logExceptionError($e)
    {
        $errorReporting = error_reporting();
        $records = array();
        $errorPriorities = $this->getErrorPriorities();
        do {
            $priority = $this->getPriority('error');
            if ($e instanceof ErrorException && isset($errorPriorities[$e->getSeverity()])) {
                $priority = $errorPriorities[$e->getSeverity()];
            }
            $extra = [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ];
            if (isset($e->xdebug_message)) {
                $extra['xdebug'] = $e->xdebug_message;
            }
            $records[] = [
                'priority' => $priority,
                'message'  => $e->getMessage(),
                'extra'    => $extra,
            ];
            $e = $e->getPrevious();
        } while ($e && $errorReporting);

        foreach (array_reverse($records) as $record) {
            $this->error($record['message'], $record['extra'], $record['priority']);
        }
    }

    /**
     * Push ( Write log handlers data )
     * 
     * @return void
     */
    public function push()
    {
        if ($this->isEnabled() == false) {
            return;
        }
        $name = end($this->loadedHandlers);
        foreach ($this->handlerRecords as $recordUnformatted) {
            $priority = $recordUnformatted['priority'];
            unset($recordUnformatted['priority']);
            $this->sendToHandlerQueue($name, $recordUnformatted, $priority);  // Send to priority queue
        }
        $this->channel($this->params['default']['channel']);    // Reset channel to default
        $this->loadedHandlers = array();  // Reset loaded handler.
        array_pop($this->track);         // Remove last track to reset handler filters
    }

    /**
     * Extract log data
     * 
     * @param object $name   PriorityQueue name
     * @param string $prefix prefix
     * 
     * @return array records
     */
    public function extract($name, $prefix = '')
    {
        $pQ = $this->getQueue($name, $prefix);
        $pQ->setExtractFlags(PriorityQueue::EXTR_DATA); // Queue mode of extraction
        $records = array();

        if ($pQ->count() > 0) {
            $pQ->top();  // Go to Top
            $i = 0;
            while ($pQ->valid()) {         // Prepare Lines
                $records[$i] = $pQ->current();
                $pQ->next();
                ++$i;
            }
        }
        return $records;
    }

    /**
     * Detect logger request type ( http, ajax, cli, worker )
     * 
     * @param Request $request request
     *
     * @todo do it logger middleware
     * 
     * @return void
     */
    protected function detectRequest($request)
    {
        $this->requestString = 'http';
        if ($request->isAjax()) {
            $this->requestString ='ajax';
        }
        if (defined('STDIN')) {
            $this->requestString = 'cli';
        }
        $server = $request->getServerParams();
        if (isset($server['argv'][1]) && $server['argv'][1] == 'worker') {  // Job Server request
            $this->requestString = 'worker';
            $this->enabled = $this->params['app']['worker']['log']; // Initialize to config if $handler->isAllowed() method ignored.
        }      
    }

     /**
     * Extract queued log handlers data store them into one array
     * 
     * @return void
     */
    protected function execWriter()
    {
        $name = $this->getWriter();
        $records = $this->extract($name);
        if (empty($records)) {
            return;
        }
        $this->payload['writers'][10]['handler'] = $name;
        $this->payload['writers'][10]['request'] = $this->requestString;
        $this->payload['writers'][10]['type']    = 'writer';
        $this->payload['writers'][10]['time']    = time();
        $this->payload['writers'][10]['filters'] = $this->getFilters($name);
        $this->payload['writers'][10]['record']  = $records; // set record array      
    }

    /**
     * Execute handler
     * 
     * @return void
     */
    public function execHandlers()
    {
        if (count($this->handlerRecords) == 0) { // If we haven't got any handler record don't parse handlers
            return;
        }
        foreach ($this->handlers as $name => $val) {  // Write log data to foreach handlers
            $records = $this->extract($name, 'handler.');
            if (empty($records)) {
                continue;
            }
            $priority = $val['priority'];
            $this->payload['writers'][$priority]['handler'] = $name;
            $this->payload['writers'][$priority]['request'] = $this->requestString;
            $this->payload['writers'][$priority]['type']    = 'handler';
            $this->payload['writers'][$priority]['time']    = time();
            $this->payload['writers'][$priority]['filters'] = $this->getFilters($name);
            $this->payload['writers'][$priority]['record']  = $records; // set record array
        }
    }

    /**
     * Returns to rendered log records
     * 
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * End of the logs and beginning of the handlers.
     *  
     * @return void
     */
    public function shutdown()
    {
        if ($this->isEnabled() && $this->isConnected()) {   // Lazy loading for Logger service
                                                            // if connect method executed one time then we open connections and load classes
                                                            // When connect booelan is true we execute standart worker or queue.
            $this->execWriter();
            $this->execHandlers();
            $payload = $this->getPayload();

            if ($this->params['queue']['enabled']) { // Queue Logger

                $this->c->get('queue')
                    ->push(
                        'Workers@Logger',
                        $this->params['queue']['job'],
                        $payload,
                        $this->params['queue']['delay']
                    );

            } else {

                $worker = new \Workers\Logger;
                $worker->setContainer($this->c);
                $worker->fire(null, $payload);
            }
        }
    }

}