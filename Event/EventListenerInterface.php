<?php

namespace Obullo\Event;

use Obullo\Container\ContainerInterface;

/**
 * Event listener class modeled after Laravel event package 
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface EventListenerInterface
{
    /**
     * Subsrice to event
     * 
     * @param object $event EventInterface
     * 
     * @return void
     */
    public function subscribe(EventInterface $event);
}