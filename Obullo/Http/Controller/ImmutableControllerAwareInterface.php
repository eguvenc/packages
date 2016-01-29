<?php

namespace Obullo\Http\Controller;

use Obullo\Http\Controller\ImmutableControllerInterface as Controller;

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
