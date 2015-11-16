<?php

namespace Obullo\Event;

use RuntimeException;
use Obullo\Container\ContainerInterface;

/**
 * Event class modeled after Laravel event package 
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Event implements EventInterface
{
    /**
     * Application
     * 
     * @var object
     */
    protected $c;

    /**
     * The event firing stack.
     *
     * @var array
     */
    protected $firing = array();

    /**
     * Listeners
     * 
     * @var array
     */
    protected $listeners = array();

    /**
     * Subscribers
     * 
     * @var array
     */
    protected $subscribers = array();

    /**
     * Create a new event dispatcher instance.
     *
     * @param object $c container
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param string|array $events   events
     * @param mixed        $listener $listener string classname or closure object
     * @param int          $priority priority of events
     * 
     * @return void
     */
    public function listen($events, $listener, $priority = 0)
    {
        if (! is_string($listener) && ! is_callable($listener)) {
            throw new RuntimeException('Listen method second parameter must be class name or closure function.');
        }
        foreach ((array) $events as $event) {
            $this->listeners[$event][$priority][] = (is_string($listener)) ?  $this->createClassListener($listener) : $listener;
            unset($this->sorted[$event]);
        }
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param string $event name
     * 
     * @return bool
     */
    public function hasListeners($event)
    {
        return isset($this->listeners[$event]);
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param string $subscriber classname
     * 
     * @return void
     */
    public function subscribe($subscriber)
    {
        if (is_string($subscriber)) {
            $Class = '\\'.$subscriber;
            $subscriber = new $Class;
            if (method_exists($subscriber, 'setContainer')) {
                $subscriber->setContainer($this->c);
            }
        }
        $subscriber->subscribe($this);
    }

    /**
     * Fire an event until the first non-null response is returned.
     *
     * @param string $event   name
     * @param array  $payload payload
     * 
     * @return mixed
     */
    public function until($event, $payload = array())
    {
        return $this->fire($event, $payload, true);
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param string $event   name
     * @param mixed  $payload event payload
     * @param bool   $halt    disable event response
     * 
     * @return array|null
     */
    public function fire($event, $payload = array(), $halt = false)
    {
        $responses = array();
        if (! is_array($payload)) {      // If an array is not given to us as the payload, we will turn it into one so
            $payload = array($payload);   // we can easily use call_user_func_array on the listeners, passing in the
        }                                 // payload to each of them so that they receive each of these arguments.
        $this->firing[] = $event;
        $listeners = $this->getListeners($event);

        foreach ($listeners as $listener) {
            $response = call_user_func_array($listener, $payload);

            if (! is_null($response) && $halt) {   // If a response is returned from the listener and event halting is enabled
                array_pop($this->firing);          // we will just return this response, and not call the rest of the event
                return $response;                  // listeners. Otherwise we will add the response on the response list.
            }
            if ($response === false) {    // If a boolean false is returned from a listener, we will stop propagating
                break;                    // the event to any further listeners down in the chain, else we keep on
            }                             // looping through the listeners and firing every one in our sequence.
            $responses[] = $response;
        }
        array_pop($this->firing);

        return $halt ? null : $responses;
    }

    /**
     * Get all of the listeners for a given event name.
     *
     * @param string $event name
     * 
     * @return array
     */
    public function getListeners($event)
    {
        if (! isset($this->sorted[$event])) {
            $this->sortListeners($event);
        }
        return $this->sorted[$event];
    }

    /**
     * Sort the listeners for a given event by priority.
     *
     * @param string $event name
     * 
     * @return array
     */
    protected function sortListeners($event)
    {
        $this->sorted[$event] = array();

        // If listeners exist for the given event, we will sort them by the priority
        // so that we can call them in the correct order. We will cache off these
        // sorted event listeners so we do not have to re-sort on every events.
        if (isset($this->listeners[$event])) {
            krsort($this->listeners[$event]);
            $this->sorted[$event] = call_user_func_array('array_merge', $this->listeners[$event]);
        }
    }

    /**
     * Create a class based listener using the IoC container.
     *
     * @param string $listener class name
     * 
     * @return closure
     */
    public function createClassListener($listener)
    {
        return function () use ($listener) {

            // If the listener has an "." sign, we will assume it is being used to delimit
            // the class name from the handle method name. This allows for handlers
            // to run multiple handler methods in a single class for convenience.
            $segments = explode('@', $listener);
            $method = count($segments) == 2 ? $segments[1] : 'handle';
            $handler = $segments[0];

            // We will make a callable of the listener instance and a method that should
            // be called on that instance, then we will pass in the arguments that we
            // received in this method into this listener class instance's methods.
            $data = func_get_args();

            if (! isset($this->subscribers[$handler])) {  // Lazy loading
                $subscriber = new $handler;
                if (method_exists($subscriber, 'setContainer')) {
                    $subscriber->setContainer($this->c);
                }
                $this->subscribers[$handler] = $subscriber;
            }
            return call_user_func_array(array($this->subscribers[$handler], $method), $data);  // Container make
        };
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param string $event name
     * 
     * @return void
     */
    public function forget($event)
    {
        unset($this->listeners[$event]);
    }

}