<?php

namespace Obullo\Cli\Task;

use Obullo\Log\LoggerInterface as Logger;

class Task
{
    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Logger exist variable
     * 
     * @var null|boolean
     */
    protected $exist;

    /**
     * Constructor
     *
     * @param object $logger \Obullo\Log\LoggerInterface
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->loggerExists();
        if ($this->exist) {
            $this->logger->debug('Cli Task Class Initialized');
        }
    }

    /**
     * Run cli task
     *
     * E.g: $this->c['task']->run('');
     * 
     * @param string  $uri   task uri
     * @param boolean $debug On / Off print debugger
     * 
     * @return void
     */
    public function run($uri, $debug = false)
    {
        $delimiter = (strpos($uri, '/') > 0) ? '/' : ' ';
        $uri = explode($delimiter, trim($uri));
        $directory = array_shift($uri);
        $segments = self::getSegments($uri);

        $host = isset($_SERVER['HTTP_HOST']) ? '--host='.$_SERVER['HTTP_HOST'] : '';  // Add http host variable if request comes from http
        $shell = PHP_PATH .' '. FPATH .'/'. TASK_FILE .' '.$directory.' '. implode('/', $segments).' '. $host;

        if ($debug) {  // Enable debug output to log folder.
            $output = preg_replace(array('/\033\[36m/', '/\033\[31m/', '/\033\[0m/'), array('', '', ''), shell_exec($shell)); // Clean cli color codes
            if ($this->exist) {
                $this->logger->debug('Cli task request', array('command' => $shell, 'output' => $output));
            }
            return $output;
        }
        shell_exec($shell . ' > /dev/null &');  // Async task

        if ($this->exist) {
            $this->logger->debug('Cli task executed', array('shell' => $shell));
        }
    }

    /**
     * Create segments
     * 
     * @param array $uri segments
     *
     * @return array
     */
    protected static function getSegments($uri)
    {
        $segments = array();
        foreach ($uri as $k => $v) {
            if (! $v) {
                $segments[$k] = 'false';
            } else {
                $segments[$k] = static::ucwordsUnderscore($v);
            }
        }
        return $segments;
    }

    /**
     * Replace underscore to spaces to use ucwords
     * 
     * Before : widgets\tutorials_a  
     * After  : Widgets\Tutorials_A
     * 
     * @param string $string namespace part
     * 
     * @return void
     */
    protected static function ucwordsUnderscore($string)
    {
        if (strpos($string, '_') > 0) {
            $str = str_replace('_', '{__DELIM__}', $string);
            $exp = explode('{__DELIM__}', $str);
            $newArray = array();
            foreach ($exp as $value) {
                $newArray[] = ucfirst($value);
            }
            return implode('_', $newArray);
        }
        return $string;
    }

    /**
     * If logger exists returns to true otherwise false
     * 
     * @return boolean
     */
    protected function loggerExists()
    {
        if (is_object($this->logger) && method_exists($this->logger, 'debug')) {
            return $this->exist = true;
        }
        return $this->exist = false;
    }

}