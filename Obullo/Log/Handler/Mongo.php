<?php

namespace Obullo\Log\Handler;

use MongoDate;
use InvalidArgumentException;

/**
 * Mongo Handler 
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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
     * @param object $mongo  $mongo service provider
     * @param array  $params mongo driver options
     */
    public function __construct($mongo, array $params)
    {
        $this->params = $params;
        $this->mongoClient = $mongo;
        $database = isset($params['database']) ? $params['database'] : null;
        $collection = isset($params['collection']) ? $params['collection'] : null;
        $this->saveOptions = isset($params['save_options']) ? $params['save_options'] : array();
        $this->saveFormat = $params['save_format'];

        self::checkConfigurations($collection, $database, $mongo);
        $this->mongoCollection = $this->mongoClient->selectCollection($database, $collection);

    }

    /**
     * Check runtime errors
     * 
     * @param string $collection name
     * @param string $database   name
     * @param object $mongo      client
     * 
     * @return void
     */
    protected static function checkConfigurations($collection, $database, $mongo)
    {
        if (null === $collection) {
            throw new InvalidArgumentException('The collection parameter cannot be empty');
        }
        if (null === $database) {
            throw new InvalidArgumentException('The database parameter cannot be empty');
        }
        if (get_class($mongo) != 'MongoClient' && get_class($mongo) != 'Mongo') {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter of type %s is invalid; must be MongoClient or Mongo instance.', 
                    is_object($mongo) ? get_class($mongo) : gettype($mongo)
                )
            );
        }
    }

    /**
    * Format log records and build lines
    *
    * @param string $event             handler log event
    * @param array  $unformattedRecord current record
    * 
    * @return array formatted record
    */
    public function arrayFormat(array $event, array $unformattedRecord)
    {
        $record = array(
            'datetime' => new MongoDate(strtotime(date($this->params['format']['date'], $event['time']))),
            'channel'  => $unformattedRecord['channel'],
            'level'    => $unformattedRecord['level'],
            'message'  => $unformattedRecord['message'],
            'context'  => null,
            'extra'    => null,
        );
        if (isset($unformattedRecord['context']['extra']) && count($unformattedRecord['context']['extra']) > 0) {
            $record['extra'] = $unformattedRecord['context']['extra']; // Default extra data format is array.
            if ($this->saveFormat['extra'] == 'json') { // if extra data format json ?
                $record['extra'] = json_encode($unformattedRecord['context']['extra'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); 
            }
            unset($unformattedRecord['context']['extra']);
        }
        if (count($unformattedRecord['context']) > 0) {
            $record['context'] = $unformattedRecord['context'];
            if ($this->saveFormat['context'] == 'json') {
                $record['context'] = json_encode($unformattedRecord['context'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
        return $record;
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
        foreach ($event['record'] as $record) {
            $records[] = $this->arrayFormat($event, $record);
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