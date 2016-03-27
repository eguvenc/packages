<?php

namespace Obullo\Authentication\Storage;

use Obullo\Tests\TestOutput;
use Obullo\Tests\TestController;

class AbstractTestStorage extends TestController
{
    /**
     * Sets identifier value to session
     * 
     * @return void
     */
    public function setIdentifier()
    {
        $this->storage->setIdentifier('test@example.com');
        $test = 'test@example.com:'.$this->storage->getLoginId();
        $this->assertEqual($this->storage->getIdentifier(), $test, "I set a fake identifier then i expect that the value is '$test'.");
        $this->storage->unsetIdentifier('test@example.com');
        $this->session->destroy();
    }

    /**
     * Returns to user identifier
     * 
     * @return mixed string|id
     */
    public function getIdentifier()
    {
        $this->storage->setIdentifier('test@example.com');
        $test = 'test@example.com:'.$this->storage->getLoginId();
        $this->assertEqual($this->storage->getIdentifier(), $test, "I set a fake identifier then i expect that the value is '$test'.");
        $this->storage->unsetIdentifier('test@example.com');
        $this->session->destroy();
    }

    /**
     * Unset identifier from session
     * 
     * @return void
     */
    public function unsetIdentifier()
    {   
        $this->storage->setIdentifier('test@example.com');
        $this->storage->unsetIdentifier('test@example.com');
        $this->assertEqual($this->storage->getIdentifier(), '__empty', "I set a fake identifier then i remove it and i expect that the value is '__empty'.");
        $this->session->destroy();
    }

    /**
     * Unset identifier from session
     * 
     * @return void
     */
    public function hasIdentifier()
    {   
        $this->storage->setIdentifier('test@example.com');
        $this->assertTrue($this->storage->hasIdentifier(), "I set a fake identifier and i expect that the value is true.");
        $this->storage->unsetIdentifier('test@example.com');
        $this->assertFalse($this->storage->hasIdentifier(), "I remove the fake identifier and i expect that the value is false.");
        $this->session->destroy();
    }

    /**
     * Register credentials to temporary storage
     * 
     * @return void
     */
    public function createTemporary()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createTemporary($credentials);
        $result = $this->storage->getCredentials('__temporary');

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create temporary credentials and i expect array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 0, "I expect that the value is 0.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 1, "I expect that the value is 1.");
        }
        $this->storage->deleteCredentials('__temporary');
        $this->session->destroy();
    }

    /**
     * Register credentials to permanent storage
     * 
     * @return void
     */
    public function createPermanent()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createPermanent($credentials);
        $result = $this->storage->getCredentials('__permanent');

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create permanent credentials and i expect array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 1, "I expect that the value is 1.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 0, "I expect that the value is 0.");
        }
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Makes temporary credentials as permanent and authenticate the user.
     * 
     * @return void
     */
    public function makePermanent()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createTemporary($credentials);
        $this->storage->makePermanent();
        $result = $this->storage->getCredentials('__permanent');

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create temporary credentials then make them as permanent and i expect array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 1, "I expect that the value is 1.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 0, "I expect that the value is 0.");
        }
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Makes permanent credentials as temporary and unauthenticate the user.
     * 
     * @return void
     */
    public function makeTemporary()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createPermanent($credentials);
        $this->storage->makeTemporary();
        $result = $this->storage->getCredentials('__temporary');

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create temporary credentials then make them as permanent and i expect array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 0, "I expect that the value is 0.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 1, "I expect that the value is 1.");
        }
        $this->storage->deleteCredentials('__temporary');
        $this->session->destroy();
    }

    /**
     * Get id of identifier without random Id value
     * 
     * @return void
     */
    public function getUserId()
    {   
        $this->assertEqual($this->storage->getUserId(), "user@example.com", "I expect that the value is user@example.com.");
        $this->session->destroy();
    }

    /**
     * Get random id
     * 
     * @return void
     */
    public function getLoginId()
    {
        $this->session->remove($this->storage->getCacheKey().'/LoginId');

        $server    = $this->request->getServerParams();
        $agentStr  = isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : null;
        $userAgent = substr($agentStr, 0, 50);
        $expected  = $this->storage->getLoginId();
        $loginId   = md5(trim($userAgent).time());

        $this->assertEqual($loginId, $expected, "I expect that the value is $loginId.");
        $this->session->destroy();
    }

    /**
     * Create login id
     * 
     * @return string
     */
    public function setLoginId()
    {
        $this->getLoginId();
        $this->session->destroy();
    }

    /**
     * Gey cache key
     * 
     * @return string
     */
    public function getCacheKey()
    {
        $paramsKey = $this->container->get('user.params')['cache']['key'];
        $this->assertEqual($paramsKey, $this->storage->getCacheKey(), "I expect the storage key equals to the service configuration key '$paramsKey'.");
        $this->session->destroy();
    }

    /**
     * Get valid memory segment key
     * 
     * @return void
     */
    public function getBlock()
    {
        $block = $this->storage->getCacheKey(). ':__permanent:' .$this->storage->getIdentifier();
        $this->assertEqual($block, $this->storage->getBlock('__permanent'), "I expect the block key equals to key '$block'.");
        $this->session->destroy();
    }

    /**
     * Get valid memory segment key
     * 
     * @return void
     */
    public function getMemoryBlockKey()
    {
        $block = $this->storage->getCacheKey(). ':__temporary:' .$this->storage->getIdentifier();
        $this->assertEqual($block, $this->storage->getBlock('__temporary'), "I expect the block key equals to key '$block'.");
        $this->session->destroy();
    }

    /**
     * Returns to storage prefix key of identity data
     * 
     * @return string
     */
    public function getUserKey()
    {
        $block = $this->storage->getCacheKey(). ':__permanent:' .$this->storage->getUserId();
        $this->assertEqual($block, $this->storage->getUserKey('__permanent'), "I expect the block key equals to key '$block'.");
        $this->session->destroy();
    }

    /**
     * Returns to memory block lifetime
     * 
     * @return integer
     */
    public function getMemoryBlockLifetime()
    {
        $params = $this->container->get('user.params');

        $this->assertEqual(
            $params['cache']['block']['permanent']['lifetime'],
            $this->storage->getMemoryBlockLifetime('__permanent'),
            "I expect the permanent block lifetime equals to service configuration lifetime value."
        );
        $this->assertEqual(
            $params['cache']['block']['temporary']['lifetime'],
            $this->storage->getMemoryBlockLifetime('__temporary'),
            "I expect the temporary block lifetime equals to service configuration lifetime value."
        );
        $this->session->destroy();
    }

    /**
     * Returns true if temporary credentials does "not" exists
     * 
     * @return void
     */
    public function isEmpty()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createPermanent($credentials);

        $this->assertFalse($this->storage->isEmpty(), "I login and i expect that the value is false.");

        $this->storage->deleteCredentials();
        $credentials = $this->storage->getCredentials();
        
        $this->assertTrue($this->storage->isEmpty(), "I delete user credentials and i expect that the value is true.");
        $this->session->destroy();
    }

    /**
     * Match the user credentials.
     * 
     * @return void
     */
    public function query()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->createPermanent($credentials);
        $result = $this->storage->query();

        $identifier = $this->container->get('user.params')['db.identifier'];
        $password   = $this->container->get('user.params')['db.password'];

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create fake credentials i expect query array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 1, "I expect that the value is equal to 1.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect identity array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 0, "I expect that the value is equal to 0.");
        }
        if ($this->assertArrayHasKey($identifier, $result, "I expect identity array has '$identifier' key.")) {
            $this->assertEqual($result['username'], $credentials['username'], "I expect that the value is equal to ".$credentials['username'].".");
        }
        if ($this->assertArrayHasKey($password, $result, "I expect identity array has '$password' key.")) {
            $this->assertEqual($result['password'], $credentials['password'], "I expect that the value is equal to ".$credentials['password'].".");
        }
        TestOutput::varDump($result);
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Update credentials
     *
     * @return void
     */
    public function setCredentials()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $data = [
            '__isAuthenticated' => 1,
            '__isTemporary' => 0,
        ];
        $this->storage->setCredentials($credentials, $data, '__permanent', 60);
        $result = $this->storage->getCredentials();

        $identifier = $this->container->get('user.params')['db.identifier'];
        $password   = $this->container->get('user.params')['db.password'];

        if ($this->assertArrayHasKey('__isAuthenticated', $result, "I create fake credentials and i expect storage array has '__isAuthenticated' key.")) {
            $this->assertEqual($result['__isAuthenticated'], 1, "I expect that the value is equal to 1.");
        }
        if ($this->assertArrayHasKey('__isTemporary', $result, "I expect identity array has '__isTemporary' key.")) {
            $this->assertEqual($result['__isTemporary'], 0, "I expect that the value is equal to 0.");
        }
        if ($this->assertArrayHasKey($identifier, $result, "I expect identity array has '$identifier' key.")) {
            $this->assertEqual($result['username'], $credentials['username'], "I expect that the value is equal to ".$credentials['username'].".");
        }
        if ($this->assertArrayHasKey($password, $result, "I expect identity array has '$password' key.")) {
            $this->assertEqual($result['password'], $credentials['password'], "I expect that the value is equal to ".$credentials['password'].".");
        }
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Get user credentials data
     * 
     * @return void
     */
    public function getCredentials()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->setCredentials($credentials, array(), '__permanent', 60);
        $result = $this->storage->getCredentials();

        $identifier = $this->container->get('user.params')['db.identifier'];
        $password   = $this->container->get('user.params')['db.password'];

        if ($this->assertArrayHasKey($identifier, $result, "I create fake credentials and i expect array has '$identifier' key.")) {
            $this->assertEqual($result['username'], $credentials['username'], "I expect that the value is equal to ".$credentials['username'].".");
        }
        if ($this->assertArrayHasKey($password, $result, "I expect array has '$password' key.")) {
            $this->assertEqual($result['password'], $credentials['password'], "I expect that the value is equal to ".$credentials['password'].".");
        }
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Deletes memory block completely
     * 
     * @return void
     */
    public function deleteCredentials()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->setCredentials($credentials, array(), '__permanent', 60);
        $this->storage->deleteCredentials();
        $result = $this->storage->getCredentials();

        $this->assertEmpty($result, "I create fake credentials then i delete them and i expect that the value is true.");
        $this->session->destroy();
    }

    /**
     * Update data
     * 
     * @return void
     */
    public function update()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->setCredentials($credentials, array(), '__permanent', 60);
        $this->storage->update('username', 'test@example.com');
        $result = $this->storage->getCredentials();

        if ($this->assertArrayHasKey('username', $result, "I create fake credentials then i expect array has 'username' key.")) {
            $this->assertEqual('test@example.com', $result['username'], "I update username value as 'test@example.com' and i expect that the value is equal to 'test@example.com'.");
        }
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Remove data
     * 
     * @return void
     */
    public function remove()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
        ];
        $this->storage->setCredentials($credentials, array(), '__permanent', 60);
        $this->storage->remove('username');
        $result = $this->storage->getCredentials();

        $this->assertArrayNotHasKey('username', $result, "I create fake credentials then i remove username key and i expect array has not 'username' key.");
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

    /**
     * Return to all sessions of current user
     * 
     * @return array
     */
    public function getUserSessions()
    {
        $credentials = [
            'username' => 'user@example.com',
            'password' => '12346',
            '__time' => time()
        ];
        $this->storage->createPermanent($credentials);
        $result  = $this->storage->getUserSessions();
        $loginId = $this->storage->getLoginId();

        if ($this->assertArrayHasKey($loginId, $result, "I create fake credentials then i expect array has '$loginId' key.")) {
            $cacheIdentifier = $result[$loginId]['key'];
            $this->assertEqual($cacheIdentifier, $this->storage->getMemoryBlockKey('__permanent'), "I expect that the value of cache identifier is equal to $cacheIdentifier.");
            $this->assertArrayHasKey('__time', $result[$loginId], "I expect array has '__time' key.");
        }
        TestOutput::varDump($result);
        $this->storage->deleteCredentials();
        $this->session->destroy();
    }

}
