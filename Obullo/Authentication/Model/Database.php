<?php

namespace Obullo\Authentication\Model;

use Pdo;
use Obullo\Authentication\Model\ModelInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Database Model
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Database implements ModelInterface
{
    protected $db;                     // Database object
    protected $fields;                 // Selected fields
    protected $tablename;              // Users tablename
    protected $columnId;               // Primary key column name
    protected $columnIdentifier;       // Username column name
    protected $columnPassword;         // Password column name
    protected $columnRememberToken;    // Remember token column name

     /**
     * Constructor
     * 
     * @param object $container container
     * @param object $params    Auth configuration & service configuration parameters
     */
    public function __construct(Container $container, array $params)
    {
        $this->tablename           = $params['db.tablename'];
        $this->columnId            = $params['db.id'];
        $this->columnIdentifier    = $params['db.identifier'];
        $this->columnPassword      = $params['db.password'];
        $this->columnRememberToken = $params['db.rememberToken'];  // RememberMe token column name

        $this->connect($container, $params);
    }

    /**
     * Set database provider connection variable ( We don't open the db connection in here ) 
     * 
     * @param object $container container
     * @param array  $params    service parameters
     * 
     * @return void
     */
    public function connect(Container $container, array $params)
    {
        $this->db = $container->get('database')->shared(
            [
                'connection' => 'default'
            ]
        );
        $this->selectFields($params);
    }

    /**
     * Build select fields
     *
     * @param array $params parameters
     * 
     * @return void
     */
    protected function selectFields(array $params)
    {
        $fields = array(
            $this->columnId,
            $this->columnIdentifier,
            $this->columnPassword,
            $this->columnRememberToken
        );
        if (! empty($params['db.fields'])) {
            $this->fields = implode(",", array_merge($fields, $params['db.fields']));
        } else {
            $this->fields = implode(",", $fields);
        }
    }

    /**
     * Execute sql query
     *
     * @param array $credentials credentials
     * 
     * @return mixed boolean|array
     */
    public function query(array $credentials)
    {
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE BINARY %s = ?', $this->fields, $this->tablename, $this->columnIdentifier))
            ->bindValue(1, $credentials[$this->columnIdentifier], PDO::PARAM_STR)
            ->execute()
            ->rowArray();
    }

    /**
     * Recalled user sql query using remember cookie
     * 
     * @param string $token rememberMe token
     * 
     * @return array
     */
    public function recallerQuery($token)
    {
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE %s = ?', $this->fields, $this->tablename, $this->columnRememberToken))
            ->bindValue(1, $token, PDO::PARAM_STR)
            ->execute()
            ->rowArray();
    }

    /**
     * Update remember me token upon every login & logout
     * 
     * @param string $token       name
     * @param array  $credentials credentials
     * 
     * @return integer
     */
    public function updateRememberToken($token, array $credentials)
    {
        return $this->db->prepare(sprintf('UPDATE %s SET %s = ? WHERE BINARY %s = ?', $this->tablename, $this->columnRememberToken, $this->columnIdentifier))
            ->bindValue(1, $token, PDO::PARAM_STR)
            ->bindValue(2, $credentials[$this->columnIdentifier], PDO::PARAM_STR)
            ->execute();
    }
}