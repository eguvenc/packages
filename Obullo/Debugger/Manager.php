<?php

namespace Obullo\Debugger;

use RuntimeException;
use Obullo\Log\Handler\Raw;
use Interop\Container\ContainerInterface as Container;

/**
 * Debugger manager
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Manager
{
    /**
     * Logger class
     * 
     * @var object
     */
    protected $logger;

    /**
     * Config
     * 
     * @var array
     */
    protected $config;

    /**
     * Container class
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     *
     * @param object $container Container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->get('logger');
        $this->config = $container->get('config')->load('providers::logger');
        $this->debugger = $container->get('config')->load('debugger');
    }

    /**
     * Display logs
     * 
     * @return string echo the log output
     */
    public function printIndex()
    {
        /**
         * View variables
         * 
         * @var string
         */
        $websocketUrl = $this->debugger['socket'];
        $debuggerOff  = (int)$this->container->get('config')['extra']['debugger'];
        $debuggerUrl  = $this->container->get('url')->getBaseUrl(INDEX_PHP.'/debugger/body');

        $env = new Environment(
            $this->container->get('request'),
            $this->container->get('session')
        );
        $envHtml = $env->printHtml();
        $cookies = $this->container->get('request')->getCookieParams();

        ob_start();
        include_once 'View.php';
        $view = ob_get_clean();
        unset($envHtml);

        return $view;
    }

    /**
     * Ping socket connection
     * 
     * @return int 1 or 0
     */
    public function ping()
    {
        if (false == preg_match(
            '#(ws:\/\/(?<host>(.*)))(:(?<port>\d+))(?<url>.*?)$#i', 
            $this->debugger['socket'], 
            $matches
        )) {
            throw new RuntimeException("Debugger socket connection error, example web socket configuration: ws://127.0.0.1:9000");
        }
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $connect = socket_connect($socket, $matches['host'], $matches['port']);
        if ($connect == 1) {
            return 1;
        } else {
            return 0;
        }
    }

}