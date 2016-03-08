<?php

namespace Obullo\Authentication\Model;

use Pdo;
use Obullo\Authentication\Model\ModelInterface;
use League\Container\ImmutableContainerAwareTrait;
use Interop\Container\ContainerInterface as Container;

/**
 * Database Model
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Database implements ModelInterface
{
    use ImmutableContainerAwareTrait;

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
     * @param Container $container container
     * @param array     $params    service params
     */
    public function __construct(Container $container, array $params)
    {
        $this->tablename           = $params['db.tablename'];
        $this->columnId            = $params['db.id'];
        $this->columnIdentifier    = $params['db.identifier'];
        $this->columnPassword      = $params['db.password'];
        $this->columnRememberToken = $params['db.rememberToken'];  // RememberMe token column name

        $this->setContainer($container);
        $this->connect();
        $this->setFields();
    }

    /**
     * Connect to database service
     * 
     * @return void
     */
    public function connect()
    {
        $this->db = $this->getContainer()->get('database')->shared(
            [
                'connection' => 'default'
            ]
        );
    }

    /**
     * Build select fields
     * 
     * @return void
     */
    public function setFields()
    {
        $this->fields = array(
            $this->getColumnId(),
            $this->getColumnIdentifier(),
            $this->getColumnPassword(),
            $this->getColumnRememberToken()
        );
    }

    /**
     * Returns to tablename
     * 
     * @return string
     */
    public function getTablename()
    {
        return $this->tablename;
    }

    /**
     * Returns to column id name
     * 
     * @return string
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * Returns to column identifier name
     * 
     * @return string
     */
    public function getColumnIdentifier()
    {
        return $this->columnIdentifier;
    }

    /**
     * Returns to column password name
     * 
     * @return string
     */
    public function getColumnPassword()
    {
        return $this->columnPassword;
    }

    /**
     * Returns to column remember token name
     * 
     * @return string
     */
    public function getColumnRememberToken()
    {
        return $this->columnPassword;
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
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE BINARY %s = ?', $this->getFields(), $this->getTablename(), $this->getColumnIdentifier()))
            ->bindValue(1, $credentials[$this->getColumnIdentifier()], PDO::PARAM_STR)
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
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE %s = ?', $this->getFields(), $this->getTablename(), $this->getColumnRememberToken()))
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
        return $this->db->prepare(sprintf('UPDATE %s SET %s = ? WHERE BINARY %s = ?', $this->getTablename(), $this->getColumnRememberToken(), $this->getColumnIdentifier()))
            ->bindValue(1, $token, PDO::PARAM_STR)
            ->bindValue(2, $credentials[$this->getColumnIdentifier()], PDO::PARAM_STR)
            ->execute();
    }
}