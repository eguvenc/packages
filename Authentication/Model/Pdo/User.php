<?php

namespace Obullo\Authentication\Model\Pdo;

use Pdo;
use Auth\Identities\AuthorizedUser;
use Obullo\Container\ServiceProviderInterface;
use Obullo\Authentication\Model\UserInterface;

/**
 * User Model
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class User implements UserInterface
{
    protected $db;                     // Database object
    protected $select;                 // Selected fields
    protected $tablename;              // Users tablename
    protected $columnId;               // Primary key column name
    protected $columnIdentifier;       // Username column name
    protected $columnPassword;         // Password column name
    protected $columnRememberToken;    // Remember token column name

     /**
     * Constructor
     * 
     * @param object $provider \Obullo\Service\ServiceProviderInterface
     * @param object $params   Auth configuration & service configuration parameters
     */
    public function __construct(ServiceProviderInterface $provider, array $params)
    {
        $this->tablename           = $params['db.tablename'];
        $this->columnId            = $params['db.id'];
        $this->columnIdentifier    = $params['db.identifier'];
        $this->columnPassword      = $params['db.password'];
        $this->columnRememberToken = $params['db.rememberToken'];  // RememberMe token column name

        $this->connect($provider, $params);
    }

    /**
     * Set database provider connection variable ( We don't open the db connection in here ) 
     * 
     * @param object $provider service provider object
     * @param array  $params   parameters
     * 
     * @return void
     */
    public function connect(ServiceProviderInterface $provider, array $params)
    {
        $this->db = $provider->get(
            [
                'connection' => $params['db.provider']['params']['connection']
            ]
        );
        $this->select($params);
    }

    /**
     * Build select fields
     *
     * @param array $params parameters
     * 
     * @return void
     */
    protected function select(array $params)
    {
        $fields = [
                $this->columnId,
                $this->columnIdentifier,
                $this->columnPassword,
                $this->columnRememberToken
            ];
        if (! empty($params['db.select'])) {
            $this->select = implode(",", array_merge($fields, $params['db.select']));
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
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE BINARY %s = ?', $this->select, $this->tablename, $this->columnIdentifier))
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
        return $this->db->prepare(sprintf('SELECT %s FROM %s WHERE %s = ?', $this->select, $this->tablename, $this->columnRememberToken))
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