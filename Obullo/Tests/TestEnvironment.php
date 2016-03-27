<?php

namespace Obullo\Tests;

/**
 * Test environment
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestEnvironment
{
    /**
     * Create unit test environment
     * 
     * @return void
     */
    public static function createServer()
    {
        if ($_SERVER['SCRIPT_FILENAME'] == 'public/index.php') {

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

        }
    }

}