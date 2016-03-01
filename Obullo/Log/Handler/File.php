<?php

namespace Obullo\Log\Handler;

/**
 * File Handler 
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class File extends AbstractHandler implements MetadataAwareInterface, HandlerInterface
{
    use MetadataAwareTrait;

    /**
     * Service configuration
     * 
     * @var array
     */
    protected $params;

    /**
     * Handler options
     * 
     * @var array
     */
    protected $options;

    /**
     * Constructor
     * 
     * @param array $params  logger service parameters
     * @param array $options handler options
     */
    public function __construct(array $params, $options = array())
    {
        $this->params = $params;
        $this->options = $options;
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
        foreach ($event['records'] as $record) {
            $record = $this->arrayFormat($record);
            $lines .= $this->lineFormat($record);
        }
        $meta = $this->getMetadata();
        $type = $meta['request'];

        if (isset($this->options['path'][$type])) {
            $path = self::getPath($this->options['path'][$type]);
            if (! $fop = fopen($path, 'ab')) {
                return false;
            }
            flock($fop, LOCK_EX);
            fwrite($fop, $lines);
            flock($fop, LOCK_UN);
            fclose($fop);
            chown($path, CHOWN);
            chmod($path, 0666);   
        }
    }

    /**
     * Returns to log path
     * 
     * @param string $path log path
     * 
     * @return string current path
     */
    protected static function getPath($path)
    {
        $path = ltrim($path, '/');
        $resourceFolder = str_replace(ROOT, "", RESOURCES);
        if (strpos($path, $resourceFolder) === 0) {    // Add root 
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