<?php

namespace Obullo\Log\Handler;

use MongoDate;
use InvalidArgumentException;

/**
 * Mongo Handler 
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Mongo extends AbstractHandler implements HandlerInterface
{
    /**
     * Service configuration
     * 
     * @var array
     */
    protected $params;

    /**
     * Save format
     * 
     * @var array
     */
    protected $saveFormat;

    /**
     * Mongo save options
     * 
     * @var array
     */
    protected $saveOptions;

    /**
     * MongoClient object
     * 
     * @var object
     */
    protected $mongoClient;

    /**
     * MongoCollection object
     * 
     * @var object
     */
    protected $mongoCollection;

    /**
     * Constructor
     * 
     * @param array  $params      logger service paramters
     * @param object $mongoClient mongo client object
     * @param array  $options     mongo driver options
     */
    public function __construct(array $params, $mongoClient, $options = array())
    {
        $this->params = $params;
        $this->mongoClient = $mongoClient;
        $database = isset($options['database']) ? $options['database'] : null;
        $collection = isset($options['collection']) ? $options['collection'] : null;
        $this->saveOptions = isset($options['options']) ? $options['options'] : array();
        $this->saveFormat = $options['encoding'];

        self::checkConfigurations($collection, $database, $mongoClient);
        $this->mongoCollection = $this->mongoClient->selectCollection($database, $collection);

    }

    /**
     * Check runtime errors
     * 
     * @param string $collection  name
     * @param string $database    name
     * @param object $mongoClient mongo client object
     * 
     * @return void
     */
    protected static function checkConfigurations($collection, $database, $mongoClient)
    {
        if (null === $collection) {
            throw new InvalidArgumentException('The collection parameter cannot be empty');
        }
        if (null === $database) {
            throw new InvalidArgumentException('The database parameter cannot be empty');
        }
        if (get_class($mongoClient) != 'MongoClient' && get_class($mongoClient) != 'Mongo') {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter of type %s is invalid; must be MongoClient or Mongo instance.', 
                    is_object($mongoClient) ? get_class($mongoClient) : gettype($mongoClient)
                )
            );
        }
    }

    /**
    * Format log records and build lines
    *
    * @param array $unformattedRecord current record
    * 
    * @return array formatted record
    */
    public function arrayFormat(array $unformattedRecord)
    {
        $record = array(
            'datetime' => new MongoDate(strtotime(date($this->params['format']['date'], time()))),
            'channel'  => $unformattedRecord['channel'],
            'level'    => $unformattedRecord['level'],
            'message'  => $unformattedRecord['message'],
            'context'  => null,
            'extra'    => null,
        );
        if (isset($unformattedRecord['context']['extra']) && count($unformattedRecord['context']['extra']) > 0) {
            $record['extra'] = $unformattedRecord['context']['extra']; // Default extra data format is array.
            if ($this->saveFormat['extra'] == 'json') { // if extra data format json ?
                $record['extra'] = self::encodeJson($unformattedRecord['context']['extra']);
            }
            unset($unformattedRecord['context']['extra']);
        }
        if (count($unformattedRecord['context']) > 0) {
            $record['context'] = $unformattedRecord['context'];
            if ($this->saveFormat['context'] == 'json') {
                $record['context'] = self::encodeJson($unformattedRecord['context']);
            }
        }
        return $record;
    }

    /**
     * Encode json
     * 
     * @param array $data data
     * 
     * @return string
     */
    protected static function encodeJson($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Writer 
     *
     * @param array $event current hanfler log event
     * 
     * @return void
     */
    public function write(array $event)
    {
        $records = array();
        foreach ($event['records'] as $record) {
            $records[] = $this->arrayFormat($record);
        }
        $this->mongoCollection->batchInsert(
            $records, 
            array_merge(
                $this->saveOptions, 
                [
                    'continueOnError' => true
                ]
            )
        );
    }

    /**
     * Close handler connection
     * 
     * @return void
     */
    public function close() 
    {
        return $this->mongoClient->close();
    }
}