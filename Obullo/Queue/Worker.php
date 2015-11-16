<?php

namespace Obullo\Queue;

use Exception;
use ErrorException;
use Obullo\Queue\Job;
use Obullo\Cli\Console;
use Obullo\Cli\UriInterface as Uri;
use Obullo\Log\LoggerInterface as Logger;
use Obullo\Queue\QueueInterface as Queue;
use Obullo\Config\ConfigInterface as Config;
use Obullo\Application\ApplicationInterface as Application;

/**
 * Queue Worker
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Worker
{
    /**
     * Job instance
     * 
     * @var object
     */
    protected $job;

    /**
     * Environment
     * 
     * @var string
     */
    protected $env;

    /**
     * Cli instance
     * 
     * @var object
     */
    protected $cli;

    /**
     * Application
     * 
     * @var object
     */
    protected $app;

    /**
     * Config
     * 
     * @var object
     */
    protected $config;

    /**
     * Queue instance
     * 
     * @var object
     */
    protected $queue;

    /**
     * Logger instance
     * 
     * @var object
     */
    protected $logger;

    /**
     * Queue name 
     * 
     * @var string
     */
    protected $route;

    /**
     * Sleep time
     * 
     * @var int
     */
    protected $sleep;

    /**
     * Job delay interval
     * 
     * @var int
     */
    protected $delay;

    /**
     * Allowed memory
     * 
     * @var int
     */
    protected $memory;

    /**
     * Max timeout
     * 
     * @var int
     */
    protected $timeout;

    /**
     * Debug Output
     * 
     * @var int
     */
    protected $output;

    /**
     * Max attempts
     * 
     * @var int
     */
    protected $attempt;

    /**
     * Queue job ( exchange )
     * 
     * @var string
     */
    protected $exchange;

    /**
     * Your custom variable
     * 
     * @var string
     */
    protected $var = null;

    /**
     * Registered error handler
     *
     * @var bool
     */
    protected static $registeredErrorHandler = false;

    /**
     * Registered exception handler
     *
     * @var bool
     */
    protected static $registeredExceptionHandler = false;

    /**
     * Registered fatal error handler
     * 
     * @var boolean
     */
    protected static $registeredFatalErrorShutdownFunction = false;

    /**
     * Error priorities
     * 
     * @var array
     */
    protected static $priorities = array(
        'emergency' => LOG_EMERG,
        'alert'     => LOG_ALERT,
        'critical'  => LOG_CRIT,
        'error'     => LOG_ERR,
        'warning'   => LOG_WARNING,
        'notice'    => LOG_NOTICE,
        'info'      => LOG_INFO,
        'debug'     => LOG_DEBUG,
    );

    /**
     * Priority Map
     *
     * @var array
     */
    protected static $errorPriorities = array(
        E_NOTICE            => LOG_NOTICE,
        E_USER_NOTICE       => LOG_NOTICE,
        E_WARNING           => LOG_WARNING,
        E_CORE_WARNING      => LOG_WARNING,
        E_USER_WARNING      => LOG_WARNING,
        E_ERROR             => LOG_ERR,
        E_USER_ERROR        => LOG_ERR,
        E_CORE_ERROR        => LOG_ERR,
        E_RECOVERABLE_ERROR => LOG_ERR,
        E_STRICT            => LOG_DEBUG,
        E_DEPRECATED        => LOG_DEBUG,
        E_USER_DEPRECATED   => LOG_DEBUG,
    );

    /**
     * Create a new queue worker.
     *
     * @param object $app    \Obullo\Application\Application
     * @param object $config \Obullo\Config\ConfigInterface
     * @param object $queue  \Obullo\Queue\QueueInterface
     * @param object $uri    \Obullo\Cli\UriInterface
     * @param object $logger \Obullo\Log\LogInterface
     */
    public function __construct(Application $app, Config $config, Queue $queue, Uri $uri, Logger $logger)
    {
        $this->app = $app;
        $this->uri = $uri;
        $this->config = $config;
        $this->queue = $queue;
        $this->logger = $logger;
        $this->logger->debug('Queue Worker Class Initialized');
    }

    /**
     * Initialize to worker object
     * 
     * @return void
     */
    public function init() 
    {
        $this->registerExceptionHandler();  // If debug closed don't show errors and use worker custom error handlers.
        $this->registerErrorHandler();      // Register worker error handlers.
        $this->registerFatalErrorHandler();
    
        ini_set('error_reporting', 0);      // Disable cli errors on console mode we already had error handlers.
        ini_set('display_errors', 0);
                                               // Don't change here we already catch all errors except the notices.
        error_reporting(E_NOTICE | E_STRICT);  // This is just Enable "Strict Errors" otherwise we couldn't see them.

        $this->exchange = $this->uri->argument('worker', null);
        $this->route = $this->uri->argument('job', null);
        $this->memory = $this->uri->argument('memory', 128);
        $this->delay  = $this->uri->argument('delay', 0);
        $this->timeout = $this->uri->argument('timeout', 0);
        $this->sleep = $this->uri->argument('sleep', 3);
        $this->attempt = $this->uri->argument('attempt', 0);
        $this->output = $this->uri->argument('output', 0);
        $this->env = $this->uri->argument('env', 'local');
        $this->var = $this->uri->argument('var', null);

        if ($this->memoryExceeded($this->memory)) {
            die; return;
        }
    }

    /**
     * Pop the next job off of the queue.
     * 
     * @return void
     */
    public function pop()
    {
        $this->job = $this->getNextJob();
        if (! is_null($this->job)) {
            $this->doJob();
            $this->debugOutput($this->job->getRawBody());
        } else {                  // If we have not job on the queue sleep the script for a given number of seconds.
            sleep($this->sleep);  // Sleep the script for a given number of seconds.
        }
    }

    /**
     * Get the next job from the queue connection.
     *
     * @return object job
     */
    protected function getNextJob()
    {
        if (is_null($this->route)) {
            return $this->queue->pop($this->exchange, $this->route);
        }
        if (! is_null($job = $this->queue->pop($this->exchange, $this->route))) { 
            return $job;
        }
    }

    /**
     * Process a given job from the queue.
     * 
     * @return void
     */
    public function doJob()
    {
        if ($this->attempt > 0 && $this->job->getAttempts() > $this->attempt) {
            $this->job->delete();
            $this->logger->channel('queue');
            $this->logger->warning(
                'The job failed and deleted from queue.', 
                array(
                    'job' => $this->job->getName(), 
                    'body' => $this->job->getRawBody()
                )
            );
            return;
        }
        $this->job->setEnv($this->env);
        $this->job->fire();
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param integer $memoryLimit sets memory limit
     * 
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Register logging system as an error handler to log PHP errors
     *
     * @param boolean $continueNativeHandler native handler switch
     * 
     * @return mixed Returns result of set_error_handler
     */
    public function registerErrorHandler($continueNativeHandler = false)
    {
        if (static::$registeredErrorHandler) {  // Only register once per instance
            return false;
        }
        $errorPriorities = static::$errorPriorities;    // We need to move priorities in this class.
        $previous = set_error_handler(
            function ($level, $message, $file, $line) use ($errorPriorities, $continueNativeHandler) {
                $iniLevel = error_reporting();
                if ($iniLevel & $level) {
                    $priority = static::$priorities['error'];
                    if (isset($errorPriorities[$level])) {
                        $priority = $errorPriorities[$level];
                    } 
                    $event = array(
                        'error_level' => $level,
                        'error_message' => $message,
                        'error_file' => $file,
                        'error_line' => $line,
                        'error_trace' => '',
                        'error_xdebug' => '',
                        'error_priority' => $priority,
                    );
                    $this->saveFailedJob($event, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10));
                }
                return ! $continueNativeHandler;
            }
        );
        static::$registeredErrorHandler = true;
        return $previous;
    }

    /**
     * Register logging system as an exception handler to log PHP exceptions
     * 
     * @return boolean
     */
    public function registerExceptionHandler()
    {
        if (static::$registeredExceptionHandler) {  // Only register once per instance
            return false;
        }
        $errorPriorities = static::$errorPriorities;  // @see http://www.php.net/manual/tr/errorexception.getseverity.php
        set_exception_handler(
            function ($exception) use ($errorPriorities) {
                $messages = array();
                do {
                    $priority = static::$priorities['error'];
                    $level = LOG_ERR;
                    if ($exception instanceof ErrorException && isset($errorPriorities[$exception->getSeverity()])) {
                        $level = $exception->getSeverity();
                        $priority = $errorPriorities[$level];
                    }
                    $messages[] = array(
                        'level' => $level,
                        'message' => $exception->getMessage(),
                        'file'  => $exception->getFile(),
                        'line'  => $exception->getLine(),
                        'trace'  => $exception->getTrace(),
                        'xdebug' => isset($exception->xdebug_message) ? $exception->xdebug_message : '',
                        'priority' => $priority,
                    );
                    $exception = $exception->getPrevious();
                } while ($exception);

                foreach (array_reverse($messages) as $message) {
                    $event = array(
                        'error_level' => $message['level'],
                        'error_message' => $message['message'], 
                        'error_file' => $message['file'],
                        'error_line' => $message['line'],
                        'error_trace' => '',
                        'error_xdebug' => $message['xdebug'],
                        'error_priority' => $message['priority'],
                    );
                    $this->saveFailedJob($event, $message['trace']);
                }
                if (! is_null($this->job) && ! $this->job->isDeleted()) { // If we catch an exception we will attempt to release the job back onto
                    $this->job->release($this->delay);  // the queue so it is not lost. This will let is be retried at a later time by another worker.
                }
            }
        );
        static::$registeredExceptionHandler = true;
        return true;
    }

    /**
     * Register a shutdown handler to log fatal errors
     * 
     * @return bool
     */
    public function registerFatalErrorHandler()
    {
        if (static::$registeredFatalErrorShutdownFunction) {  // Only register once per instance
            return false;
        }          
        register_shutdown_function(
            function () {
                if (null != $error = error_get_last()) {
                    $event = array(
                        'error_level' => $error['type'],
                        'error_message' => $error['message'], 
                        'error_file' => $error['file'],
                        'error_line' => $error['line'],
                        'error_trace' => '',
                        'error_xdebug' => '',
                        'error_priority' => 99,
                    );
                    $this->saveFailedJob($event);
                }
            }
        );
        static::$registeredFatalErrorShutdownFunction = true;
        return true;
    }

    /**
     * Save failed job to database
     * 
     * @param array $event      failed data
     * @param array $errorTrace error trace (optional)
     * 
     * @return void
     */
    protected function saveFailedJob(array $event, $errorTrace = null)
    {
        // Worker does not well catch failed job exceptions because of we
        // use this function in exception handler.Thats the point why we need to try catch block.

        $params = $this->config['queue'];
        if (! $params['failedJob']['enabled']) {
            return;
        }
        $event = $this->prependJobDetails($event);
        if ($this->output) {
            $this->debugOutput($event);
        }
        $storageClassName = '\\'.ltrim($params['failedJob']['storage'], '\\');
        $storage = new $storageClassName(
            $this->config,
            $this->app->provider($params['failedJob']['provider']['name']),
            $params
        );
        $storage->save($event, $errorTrace);
    }

    /**
     * Append job event to valid array
     * 
     * @param array $event array
     * 
     * @return array merge event
     */
    protected function prependJobDetails(array $event)
    {
        if (! is_object($this->job)) {
            return $event;
        }
        return array_merge(
            $event,
            array(
                'job_id' => $this->job->getId(),
                'job_name' => $this->job->getName(),
                'job_body' => $this->job->getRawBody(),
                'job_attempts' => $this->job->getAttempts()
            )
        );
    }

    /**
     * Unregister error handler
     *
     * @return void
     */
    public static function unregisterErrorHandler()
    {
        restore_error_handler();
        static::$registeredErrorHandler = false;
    }

    /**
     * Unregister exception handler
     *
     * @return void
     */
    public function unregisterExceptionHandler()
    {
        restore_exception_handler();
        static::$registeredExceptionHandler = false;
    }

    /**
     * Print errors and output
     * 
     * @param array|string $event output
     * 
     * @return void
     */
    public function debugOutput($event)
    {
        if (is_string($event)) {
            echo Console::text("Output : \n".$event."\n", 'yellow');
        } elseif (is_array($event)) {
            unset($event['error_trace']);
            unset($event['error_xdebug']);
            unset($event['error_priority']);
            echo Console::fail("Error : \n".print_r($event, true));
        }
    }

}