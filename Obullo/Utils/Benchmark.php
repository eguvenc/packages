<?php

namespace Obullo\Utils;

use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Benchmark helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Benchmark
{
    protected static $time;

    /**
     * Start app benchmark
     * 
     * @param Request $request object
     * 
     * @return object
     */
    public static function start(Request $request)
    {
        self::$time = microtime(true);

        return $request->withAttribute('REQUEST_TIME_START', self::$time);
    }

    /**
     * Finalize benchmark
     * 
     * @param Request $request request
     * @param array   $extra   extra
     * 
     * @return void
     */
    public static function end($request, $extra = array())
    {
        global $container;
        
        $logger = $container->get('logger');
        $config = $container->get('config')->get('config');

        $time  = ($request == null) ? self::$time : $request->getAttribute('REQUEST_TIME_START');

        $end = microtime(true) - $time;
        $usage = 'memory_get_usage() function not found on your php configuration.';
        
        if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '') {
            $usage = round($usage/1024/1024, 2). ' MB';
        }
        if ($config['extra']['debugger']) {  // Exclude debugger cost from benchmark results.
            $end = $end - 0.0003;
        }
        $extra['time']   = number_format($end, 4);
        $extra['memory'] = $usage;
        
        $logger->debug('Final output sent to browser', $extra, -9999);
    }

}