<?php

namespace Obullo\Http;

use Obullo\Http\ImmutableControllerInterface as Controller;

/**
 * Inject controller
 */
interface ImmutableControllerAwareInterface
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
