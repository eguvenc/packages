<?php

namespace Obullo\Http;

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
