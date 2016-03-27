<?php

namespace Obullo\Application;

/**
 * Interface Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ApplicationInterface
{
    /**
     * Returns current version of Obullo
     * 
     * @return string
     */
    public function getVersion();

    /**
     * Returns to container object
     * 
     * @return string
     */
    public function getContainer();

}