<?php

namespace Obullo\Queue;

use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\ServiceInterface;
use Obullo\Queue\Handler\AmqpLib;

/**
 * Queue Service Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class QueueManagerAmqpLib implements ServiceInterface
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
     * @param ContainerInterface $container container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Set service parameters
     * 
     * @param array $params service configuration
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->c['queue.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['queue'] = function () {

            $name   = $this->c['queue.params']['provider']['name'];
            $params = $this->c['queue.params']['provider']['params'];

            return new AmqpLib(
                $this->c['config'],
                $this->c[$name],
                $params
            );

        };
    }

}