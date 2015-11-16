<?php

namespace Obullo\Log;

/**
 * Log Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function emergency($message = '', $context = array(), $priority = null);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function alert($message = '', $context = array(), $priority = null);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function critical($message = '', $context = array(), $priority = null);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function error($message = '', $context = array(), $priority = null);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function warning($message = '', $context = array(), $priority = null);

    /**
     * Normal but significant events.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function notice($message = '', $context = array(), $priority = null);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function info($message = '', $context = array(), $priority = null);

    /**
     * Detailed debug information.
     *
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function debug($message = '', $context = array(), $priority = null);

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level    level
     * @param string  $message  message
     * @param array   $context  data
     * @param integer $priority priority of log
     * 
     * @return null
     */
    public function log($level, $message, $context = array(), $priority = null);
}