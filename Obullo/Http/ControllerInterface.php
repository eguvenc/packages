<?php

namespace Obullo\Http;

use Obullo\Container\ContainerInterface as Container;

/**
 * Interface Controller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ControllerInterface
{
    /**
     * Set container
     * 
     * @param Container $container container object
     * 
     * @return void
     */
    public function __setContainer(Container $container = null);
}