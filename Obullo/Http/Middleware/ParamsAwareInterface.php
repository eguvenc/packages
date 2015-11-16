<?php

namespace Obullo\Http\Middleware;

/**
 * Inject array parameters
 */
interface ParamsAwareInterface
{
    /**
     * Inject array parameters
     * 
     * @param array $params parameters
     * 
     * @return void
     */
    public function setParams(array $params);
}
