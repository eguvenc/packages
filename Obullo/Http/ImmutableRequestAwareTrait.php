<?php

namespace Obullo\Http;

use Psr\Http\Message\ServerRequestInterface as Request;

trait ImmutableRequestAwareTrait
{
    /**
     * Request
     * 
     * @var array
     */
    protected $request;

    /**
     * Set params
     *
     * @param object $request Psr\Http\Message\ServerRequestInterface;
     * 
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }
}
