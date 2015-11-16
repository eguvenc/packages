<?php

namespace Obullo\Log\Handler;

use Obullo\Container\ContainerInterface as Container;

/**
 * File Handler 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class File extends AbstractHandler implements HandlerInterface
{
    /**
     * Service configuration
     * 
     * @var array
     */
    protected $params;

    /**
     * Handler configuration
     * 
     * @var array
     */
    protected $config;

    /**
     * Constructor
     * 
     * @param array $params logger service parameters
     * @param array $config config parameters
     */
    public function __construct(array $params, $config = array())
    {
        $this->params = $params;
        $this->config = $config;
    }

    /**
     * Write output
     *
     * @param string $event single log event
     * 
     * @return mixed
     */
    public function write(array $event)
    {
        $lines = '';
        $path  = '';
        foreach ($event['record'] as $record) {
            $record = $this->arrayFormat($event, $record);
            $lines .= $this->lineFormat($record);
        }
        $type = $event['request'];
        if ($type == 'worker') {
            $type = 'cli';
        }
        if (isset($this->config['path'][$type])) {
            $path = self::resolvePath($this->config['path'][$type]);
        }
        if (! $fop = fopen($path, 'ab')) {
            return false;
        }
        flock($fop, LOCK_EX);
        fwrite($fop, $lines);
        flock($fop, LOCK_UN);
        fclose($fop);
    }

    /**
     * If log path has "data/logs" folder, we replace it with "DIRECTORY_SEPERATOR".
     * 
     * @param string $path log path
     * 
     * @return string current path
     */
    protected static function resolvePath($path)
    {
        $path = ltrim($path, '/');
        if (strpos($path, "resources/") === 0) {    // Add root 
            return ROOT .$path;
        }
        return $path;
    }

    /**
     * Close handler connection
     * 
     * @return void
     */
    public function close()
    {
        return;
    }
}