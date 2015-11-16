<?php

namespace Obullo\Http\Middleware;

/**
 * Terminable middleware
 */
interface TerminableInterface
{
    /**
     * Terminate operations
     * 
     * @return void
     */
    public function terminate();
}
