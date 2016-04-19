<?php

namespace Obullo\Authentication\Model;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Authentication\Model\ModelInterface;

/**
 * Mongo Model
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Mongo implements ModelInterface
{
    use ContainerAwareTrait;

    protected $collection;             // Mongo collection
    protected $fields;                 // Selected fields
    protected $tablename;              // Users tablename
    protected $columnId;               // Primary key column name
    protected $columnIdentifier;       // Username column name
    protected $columnPassword;         // Password column name
    protected $columnRememberToken;    // Remember token column name

    /**
     * Constructor
     * 
     * @param array $params service params
     */
    public function __construct(array $params)
    {
        $this->tablename           = $params['db.tablename'];
        $this->columnId            = $params['db.id'];
        $this->columnIdentifier    = $params['db.identifier'];
        $this->columnPassword      = $params['db.password'];
        $this->columnRememberToken = $params['db.rememberToken'];  // RememberMe token column name
        
        $this->setFields();
    }

    /**
     * Connect to database service
     * 
     * @return void
     */
    public function connect()
    {
        $this->collection = $this->getContainer()->get('mongo')->shared(
            [
                'connection' => 'default'
            ]
        )
            ->cms
            ->users;
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
     * Build select fields
     * 
     * @return void
     */
    public function getFields()
    {
        return $this->fields;
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
        return $this->columnRememberToken;
    }

    /**
     * Execute mongo query
     *
     * @param array $credentials credentials
     * 
     * @return mixed boolean|array
     */
    public function query(array $credentials)
    {
        $row = $this->collection->findOne(
            array(
                $this->getColumnIdentifier() => $credentials[$this->getColumnIdentifier()]
            ),
            $this->getFields()
        );
        if (empty($row)) {
            return false;
        }
        return $row;
    }

    /**
     * Recalled user sql query using remember cookie
     * 
     * @param string $token rememberMe token
     * 
     * @return mixed boolean|array
     */
    public function recallerQuery($token)
    {
        $row = $this->collection->finOne(
            array(
                $this->getColumnRememberToken() => $token
            ),
            $this->getFields()
        );
        if (empty($row)) {
            return false;
        }
        return $row;
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
        return $this->collection->update(
            [
                $this->getColumnIdentifier() => $credentials[$this->getColumnIdentifier()]
            ],
            [
                '$set' => array($this->getColumnRememberToken() => $token),
            ]
        );
    }

}