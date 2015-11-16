<?php

namespace Obullo\Queue;

use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\ServiceInterface;
use Obullo\Queue\Handler\Amqp;

/**
 * Queue Service Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class QueueManagerAmqp implements ServiceInterface
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

            $name = $this->c['queue.params']['provider']['name'];
            $params = $this->c['queue.params']['provider']['params'];

            return new Amqp(
                $this->c['config'],
                $this->c[$name],
                $params
            );

        };
    }

}