<?php

namespace Obullo\Tests;

use Exception;
use ErrorException;
use Obullo\Container\ContainerAwareTrait;

/**
 * Test environment
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestEnvironment
{
    use ContainerAwareTrait;

    /**
     * Test Interface Exceptions
     * 
     * @var array
     */
    protected $testExceptions = array();

    /**
     * Create unit test environment
     * 
     * @return void
     */
    public function createServer()
    {
        $_SERVER['SERVER_NAME'] = "PHP_TEST";
        $_SERVER['HTTP_USER_AGENT'] = 'Cli Php Test';  // Define cli headers for any possible isset errors.
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_HOST'] = "";
        $_SERVER['REQUEST_URI'] = "/".ltrim(implode("", array_slice($_SERVER['argv'], 1)), "/");
        $_parseUrl = parse_url($_SERVER['REQUEST_URI']);
        $_SERVER['QUERY_STRING'] = isset($_parseUrl['query']) ? $_parseUrl['query'] : "";
        /**
         * Query params support
         */
        if (empty($_SERVER['QUERY_STRING'])) {
            $_GET = array();
        } else {
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        }
        ini_set('display_errors', 0);
        error_reporting(0);
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(array($this, 'handleFatalError'));
    }

    /**
     * Error handler, convert all errors to exceptions
     * 
     * @param integer $level   name
     * @param string  $message error message
     * @param string  $file    file
     * @param integer $line    line
     * 
     * @return boolean whether to continue displaying php errors
     */
    public function handleError($level, $message, $file = '', $line = 0)
    {
        $e = new ErrorException($message, $level, 0, $file, $line);
        $this->showException($e);
    }

    /**
     * Exception error handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function handleException(Exception $e)
    {
        $this->showException($e);
    }

    /**
     * Handle fatal errors
     * 
     * @return mixed
     */
    public function handleFatalError()
    {   
        if (null != $error = error_get_last()) {
            $e = new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
            $this->showException($e);
        }
        if (! empty($this->testExceptions)) {
            echo json_encode($this->testExceptions[0]);
        }
    }

    /**
     * Show exception
     * 
     * @param Exception $e object
     * 
     * @return void
     */
    protected function showException(Exception $e)
    {
        $container = $this->getContainer();
        /**
         * Http test inteface support
         * We show exceptions per one http request.
         */
        $queryParams = $container->get('request')->getQueryParams();

        if (! empty($queryParams['suite'])) {

            do {
                $this->testExceptions[] = [
                    'type' => get_class($e),
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            } while ($e = $e->getPrevious());
        }
    }
}