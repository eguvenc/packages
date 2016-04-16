<?php

namespace Obullo\Http\Zend\Stratigility;

use SplQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Iterate a queue of middlewares and execute them.
 */
class Next
{
    /**
     * @var Dispatch
     */
    private $dispatch;

    /**
     * @var Callable
     */
    private $done;

    /**
     * @var SplQueue
     */
    private $queue;

    /**
     * Constructor.
     *
     * Clones the queue provided to allow re-use.
     *
     * @param SplQueue $queue queue
     * @param callable $done  done
     */
    public function __construct(SplQueue $queue, callable $done)
    {
        $this->queue = clone $queue;
        $this->done  = $done;
        $this->dispatch = new Dispatch;
    }

    /**
     * Call the next Route in the queue.
     *
     * Next requires that a request and response are provided; these will be
     * passed to any middleware invoked, including the $done callable, if
     * invoked.
     *
     * If the $err value is not null, the invocation is considered to be an
     * error invocation, and Next will search for the next error middleware
     * to dispatch, passing it $err along with the request and response.
     *
     * Once dispatch is complete, if the result is a response instance, that
     * value will be returned; otherwise, the currently registered response
     * instance will be returned.
     *
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param null|mixed             $err      error
     * 
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $err = null
    ) {
        $dispatch = $this->dispatch;
        $done     = $this->done;

        // No middleware remains; done
        // 
        if ($this->queue->isEmpty()) {
            return $done($request, $response, $err);
        }

        $layer = $this->queue->dequeue();
        $result = $dispatch($layer, $err, $request, $response, $this);

        return ($result instanceof ResponseInterface ? $result : $response);
    }
}
