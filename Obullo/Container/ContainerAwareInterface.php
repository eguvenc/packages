<?php

namespace Obullo\Container;

/**
 * ContainerAware Interface
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ContainerAwareInterface
{
    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $c object or null
     *
     * @return void
     */
    public function setContainer(ContainerInterface $c = null);
}