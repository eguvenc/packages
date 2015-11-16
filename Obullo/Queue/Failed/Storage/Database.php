<?php

namespace Obullo\Queue\Failed\Storage;

use PDO;
use Exception;
use RuntimeException;
use SimpleXMLElement;
use Obullo\Config\ConfigInterface;
use Obullo\Queue\Failed\StorageInterface;
use Obullo\Container\ServiceProviderInterface;

/**
 * FailedJob Database Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Database implements StorageInterface
{
    /**
     * Db instance
     * 
     * @var object
     */
    protected $db;

    /**
     * Db tablename
     * 
     * @var array
     */
    protected $tablename;
    
    /**
     * Constuctor
     *
     * @param object $config   \Obullo\Config\ConfigInterface
     * @param object $provider \Obullo\Service\ServiceProviderInterface
     * @param object $params   service parameters
     */
    public function __construct(ConfigInterface $config, ServiceProviderInterface $provider, array $params)
    {
        $database = $config->load('database');
        $connection = $params['failedJob']['provider']['params']['connection'];

        if (! isset($database['connections'][$connection])) {
            throw new RuntimeException(
                sprintf(
                    'Failed job database connection "%s" is not defined in your database.php config file.',
                    $connection
                )
            );
        }
        $this->db = $provider->get($params['failedJob']['provider']['params']);
        $this->tablename = $params['failedJob']['table'];
    }

    /**
     * Insert failed event data to storage
     * 
     * @param array $event      key value data
     * @param array $errorTrace error trace (optional)
     * 
     * @return void
     */
    public function save(array $event, $errorTrace = null)
    {
        $event['error_trace'] = $errorTrace;

        if ($id = $this->exists($event['error_file'], $event['error_line'])) {
            $this->update($id, $event);
            return;
        }
        try {
            $this->db->beginTransaction();

            // Json encode coult not encode the large data
            // Xml Encoding fix the issue, if you see any problem please open an issue from github.
            if (! empty($event['error_trace'])) {
                $xml = new SimpleXMLElement('<root/>');
                array_walk_recursive($event['error_trace'], array($xml, 'addChild'));
                $event['error_trace'] = $xml->asXML();
            }
            if (! empty($event['error_xdebug'])) {
                $xml = new SimpleXMLElement('<root/>');
                $xml->addChild('xdebug', $event['error_xdebug']);
                $event['error_xdebug'] = $xml->asXML();
            }
            $event['failure_first_date'] = time();

            $sql = "INSERT INTO $this->tablename (".implode(',', array_keys($event)).") VALUES (".implode(',', array_values($this->db->escape($event))).")";
            $this->db->exec($sql);
            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            $exception = new \Obullo\Error\Exception;
            $exception->show($e);
        }
    }

    /**
     * Check same error is daily exists
     *
     * @param string  $file error file
     * @param integer $line error line
      * 
     * @return void
     */
    public function exists($file, $line)
    {
        $row = $this->db->prepare("SELECT id FROM $this->tablename WHERE error_file = ? AND error_line = ? LIMIT 1")
            ->bindParam(1, $file, PDO::PARAM_STR)
            ->bindParam(2, $line, PDO::PARAM_INT)
            ->execute()
            ->row();
        if ($row == false) {
            return false;
        }
        return $row->id;
    }

    /**
     * Update attempts
     * 
     * @param integer $id    queue failure id
     * @param integer $event data
     * 
     * @return void
     */
    public function update($id, array $event)
    {
        $sql = "UPDATE $this->tablename SET job_attempts = %d, failure_last_date = %d, failure_repeat = failure_repeat + 1 WHERE id = %d";
        try {
            $this->db->beginTransaction();
            $this->db->exec(sprintf($sql, $event['job_attempts'], time(), $id));
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $exception = new \Obullo\Error\Exception;
            $exception->show($e);
        }
    }

    /**
     * Get database connection
     * 
     * @return object
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Returns to tablename in service parameters
     * 
     * @return string
     */
    public function getTablename()
    {
        return $this->tablename;
    }

}