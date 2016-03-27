<?php

namespace Obullo\Error;

use Obullo\Http\Stream;
use Obullo\Cli\NullRequest;

/**
 * Exception Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Exception
{
    /**
     * Get exception view with http stream body
     * 
     * @param object $e exception
     * 
     * @return string view
     */
    public function withBody($e)
    {
        $html = $this->make($e);

        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($html);
        return $body;
    }
    
    /**
     * Display the exception view
     * 
     * @param object $e exception
     * 
     * @return string view
     */
    public function make($e)
    {
        if (! $this->isDisplayable($e)) {
            return;
        }
        return $this->display($e);
    }

    /**
     * Check whether to exception is development error if 
     * not we display it to developer
     *
     * @param object $e exception
     * 
     * @return boolean
     */
    public function isDisplayable($e)
    {
        if (strpos($e->getMessage(), 'shmop_') === 0) {  // Disable shmop function errors.
            return false;
        }
        if (strpos($e->getMessage(), 'socket_connect') === 0) {  // Disable debugger socket connection errors.
            return false;
        }
        return true;
    }

    /**
     * Display exception view
     * 
     * @param ErrorException $e error exception object
     * 
     * @return string
     */
    protected function display($e)
    {
        if (defined('STDIN')) {

            global $container;  // Test package support
            $queryParams = $container->get('request')->getQueryParams();

            if (! empty($queryParams['suite'])) {
                return json_encode(
                    [
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]
                );
            }
            return $this->view('console', $e);
        }
        $HTTP_X_REQUESTED_WITH = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');
        if (strtolower($HTTP_X_REQUESTED_WITH) === 'xmlhttprequest') {
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
        include OBULLO . 'Error/view/' .$file .'.php';
        return ob_get_clean();
    }

}