<?php

namespace Obullo\Container;

/**
 * Inject parameters
 */
interface RequestAwareInterface
{
    /**
     * Set params
     *
     * @param object $request Obullo\Http\ServerRequest
     * 
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * Get params
     *
     * @return array
     */
    public function getRequest();
}
