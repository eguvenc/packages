<?php

namespace Obullo\Session;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Session Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class SessionManager implements ServiceInterface
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
        $this->c = $c;
        $params['locale']['timezone'] = $this->c['config']['locale']['timezone'];
        $this->c['session.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['session'] = function () {

            $params = $this->c['session.params'];
            $provider = $params['provider']['name'];

            return new Session(
                $this->c[$provider],  // Service Provider
                $this->c['request'],
                $this->c['logger'],
                $params
            );

        };
    }

}