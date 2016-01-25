<?php

namespace Obullo\Container;

use Obullo\Container\ControllerInterface as Controller;

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
