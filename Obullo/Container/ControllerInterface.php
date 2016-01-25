<?php

namespace Obullo\Container;

use Interop\Container\ContainerInterface as InteropContainerInterface;

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
     * @param InteropContainerInterface $container container object
     * 
     * @return void
     */
    public function setContainer(InteropContainerInterface $container);

    /**
     * Get the container
     *
     * @return \League\Container\ImmutableContainerInterface
     */
    public function getContainer();
    
}