<?php

namespace Obullo\Config;

/**
 * Config Aware Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConfigAwareInterface
{
    /**
     * Set configuration array or object
     * 
     * @param mixed $config array|object
     */
    public function setConfig($config);
}