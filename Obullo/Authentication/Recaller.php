<?php

namespace Obullo\Authentication;

use League\Container\ContainerInterface as Container;

use Obullo\Authentication\Model\ModelInterface as Model;
use Obullo\Authentication\IdentityInterface as Identity;
use Obullo\Authentication\Storage\StorageInterface as Storage;

/**
 * Recaller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Recaller
{
    /**
     * Model
     * 
     * @var object
     */
    protected $model;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Storage
     * 
     * @var object
     */
    protected $storage;

    /**
     * User identity
     * 
     * @var object
     */
    protected $identity;

    /**
     * Datababase identifier column ( username or email .. )
     * 
     * @var string
     */
    protected $columnIdentifier;

    /**
     * Remember token column name
     * 
     * @var string
     */
    protected $columnRememberToken;

    /**
     * Constructor
     * 
     * @param object $container \Obullo\Container\Container
     * @param object $storage   \Obullo\Authentication\Storage\Storage
     * @param object $model     \Obullo\Authetication\Model\User
     * @param array  $identity  \Obullo\Authentication\Identity\Identity
     * @param array  $params    auth parameters
     */
    public function __construct(Container $container, Storage $storage, Model $model, Identity $identity, array $params)
    {
        $this->model = $model;
        $this->params = $params;
        $this->storage = $storage;
        $this->identity = $identity;
        $this->container = $container;

        $this->columnIdentifier = $params['db.identifier'];
        $this->columnRememberToken = $params['db.rememberToken'];
    }

    /**
     * Recall user identity using remember token
     * 
     * @param string $token remember me cookie
     * 
     * @return void
     */
    public function recallUser($token)
    {
        $resultRowArray = $this->model->recallerQuery($token);

        if (! is_array($resultRowArray)) {            // If login query not success.
            $this->storage->setIdentifier('Guest');   // Mark user as guest
            $this->identity->forgetMe();
            return;
        }
        $id = $resultRowArray[$this->columnIdentifier];
        $this->storage->setIdentifier($id);

        $credentials = [
            $this->columnIdentifier => $id,
            '__rememberMe' => 1,
            '__rememberToken' => $resultRowArray[$this->columnRememberToken]
        ];
        $this->identity->setCredentials($credentials);
        
        $this->container->get('auth.adapter')->generateUser($credentials, $resultRowArray);  // Generate authenticated user without validation
        $this->removeInactiveSessions(); // Kill all inactive sessions of current user
    }

    /**
     * Destroy all inactive sessions of the user
     * 
     * @return void
     */
    protected function removeInactiveSessions()
    {
        $sessions = $this->storage->getUserSessions();

        if (sizeof($sessions) == 0) {
            return;
        }
        foreach ($sessions as $loginID => $val) {       // Destroy all inactive sessions
            if (isset($val['__isAuthenticated']) && $val['__isAuthenticated'] == 0) {  
                $this->storage->killSession($loginID);
            }
        }
    }
}