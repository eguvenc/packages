<?php

namespace Obullo\Application;

use Closure;
use Exception;

/**
 * Interface Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ApplicationInterface
{
    /**
     * Set error handlers
     *
     * @return void
     */
    public function registerErrorHandlers();

    /**
     * Register fatal error handler
     * 
     * @return mixed
     */
    public function registerFatalError();

    /**
     * Sets application exception errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function error(Closure $closure);

    /**
     * Sets application fatal errors
     * 
     * @param Closure $closure function
     * 
     * @return void
     */
    public function fatal(Closure $closure);

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
    public function handleError($level, $message, $file = '', $line = 0);

    /**
     * Exception error handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function handleException(Exception $e);

    /**
     * Exception log handler
     * 
     * @param Exception $e exception class
     * 
     * @return boolean
     */
    public function exceptionError($e);

    /**
     * Returns to fatal error closure
     * 
     * @return Closure object
     */
    public function getFatalCallback();

    /**
     * Returns to defined exception closures in app/errors.php
     * 
     * @return array
     */
    public function getExceptions();

    /**
     * Is Cli ?
     *
     * Test to see if a request was made from the command line.
     *
     * @return bool
     */
    public function isCli();

    /**
     * Returns to detected environment
     * 
     * @return string
     */
    public function getEnv();

    /**
     * Returns current version of Obullo
     * 
     * @return string
     */
    public function getVersion();
}