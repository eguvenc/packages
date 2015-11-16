<?php

namespace Obullo\Authentication;

use Obullo\Authentication\User\Login;
use Obullo\Authentication\User\Activity;
use Obullo\Authentication\User\Identity;
use Obullo\Authentication\User\UserInterface;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Auth Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AuthManager implements ServiceInterface, UserInterface
{
    /**
     * Container class
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     * 
     * @param ContainerInterface $c      container
     * @param array              $params service parameters
     */
    public function __construct(Container $c, array $params)
    {
        $c['auth.params'] = $params;
        $this->c = $c;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        include VENDOR .'ircmaxell/password-compat/lib/password.php';

        $this->init();

        $this->c['user'] = function () {
            return $this;
        };
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function init()
    {
        $parameters = $this->c['auth.params'];

        $this->c['auth.storage'] = function () use ($parameters) {
            return new $parameters['cache']['storage']($this->c['session'], $this->c['cache'], $parameters);
        };
        
        $this->c['auth.identity'] = function () use ($parameters) {
            return new Identity($this->c, $this->c['session'], $this->c['auth.storage'], $parameters);
        };

        $this->c['auth.login'] = function () use ($parameters) {
            return new Login($this->c, $this->c['event'], $this->c['auth.storage'], $parameters);
        };

        $this->c['auth.activity'] = function () {
            return new Activity($this->c['auth.storage'], $this->c['auth.identity']);
        };

        $this->c['auth.model'] = function () use ($parameters) {
            $provider = $parameters['db.provider']['name'];
            return new $parameters['db.model']($this->c[$provider], $parameters);
        };

        $this->c['auth.adapter'] = function () use ($parameters) {
            return new $parameters['db.adapter'](
                $this->c,
                $this->c['session'],
                $this->c['auth.storage'],
                $parameters
            );
        };
    }

    /**
     * User service class loader
     * 
     * @param string $class name
     * 
     * @return object | null
     */
    public function __get($class)
    {
        return $this->c['auth.'.strtolower($class)]; // Services: $this->user->login, $this->user->identity, $this->user->activity ..
    }
}