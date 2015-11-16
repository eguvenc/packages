<?php

namespace Obullo\Error;

use Obullo\Http\Stream;
use Obullo\Cli\NullRequest;

/**
 * Exception Class
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Exception
{
    /**
     * Get exception view with http stream body
     * 
     * @param object  $e          exception object
     * @param boolean $fatalError whether to fatal error
     * 
     * @return string view
     */
    public function withBody(\Exception $e, $fatalError = false)
    {
        $html = $this->make($e, $fatalError);

        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($html);
        return $body;
    }
    
    /**
     * Display the exception view
     * 
     * @param object  $e          exception object
     * @param boolean $fatalError whether to fatal error
     * 
     * @return string view
     */
    public function make(\Exception $e, $fatalError = false)
    {
        if (! $this->isDisplayable($e)) {
            return;
        }

        return $this->display($e, $fatalError);
    }

    /**
     * Check whether to exception is development error if 
     * not we display it to developer
     *
     * @param object $e exception object
     * 
     * @return boolean
     */
    public function isDisplayable($e)
    {
        if (self::hasPerformanceBoostErrors($e)) {  // Disable http controller file_exists errors.
            return false;
        }
        if (strpos($e->getMessage(), 'shmop_') === 0) {  // Disable shmop function errors.
            return false;
        }
        if (strpos($e->getMessage(), 'socket_connect') === 0) {  // Disable debugger socket connection errors.
            return false;
        }
        return true;
    }

    /**
     * Check exception is catchable from app/errors.php
     *
     * @param object $e exception object
     * 
     * @return boolean
     */
    public function isCatchable($e)
    {
        if (self::hasPerformanceBoostErrors($e)) {  // Disable http controller file_exists errors.
            return false;
        }
        return true;
    }

    /**
     * Display exception view
     * 
     * @param ErrorException $e          error exception object
     * @param boolean        $fatalError bool
     * 
     * @return string
     */
    protected function display($e, $fatalError = false)
    {
        global $c;
        $request = $c['request'];

        if ($fatalError == false) { 
            unset($fatalError);  // Fatal error variable used in view file
        }
        if (defined('STDIN')) {
            return $this->view('console', $e);
        }
        if ($request->isAjax()) {
            return $this->view('ajax', $e);
        }
        return '<!DOCTYPE html> 
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <meta name="robots" content="noindex,nofollow" />
                <style>

                </style>
            </head>
            <body><div>'.$this->view('html', $e).'</div></body></html>';
    }

    /**
     * Disable include 404 include file errors
     * 
     * @param \Exception $e exception
     * 
     * @return boolean
     */
    protected static function hasPerformanceBoostErrors(\Exception $e)
    {
        if ($e->getCode() == 2 
            && substr($e->getFile(), -9) == 'Layer.php' 
            || substr($e->getFile(), -20) == 'Application/Http.php'
        ) {
            return true;
        }
        return false;
    }

    /**
     * Load exception view
     * 
     * @param string $file content
     * @param string $e    exception object
     * 
     * @return string
     */
    protected function view($file, $e)
    {   
        ob_start();
        include OBULLO . 'Error/view/' .$file . '.php';
        return ob_get_clean();
    }

}