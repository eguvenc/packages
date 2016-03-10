<?php

namespace Obullo\Http\Controller;

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
    public function setController($controller);
}
