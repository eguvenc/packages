<?php

namespace Obullo\Http\Middleware;

use Obullo\Http\ControllerInterface as Controller;

/**
 * Inject controller
 */
interface ControllerAwareInterface
{
    /**
     * Inject controller object
     * 
     * @param Controller $controller object
     * 
     * @return void
     */
    public function setController(Controller $controller);
}
