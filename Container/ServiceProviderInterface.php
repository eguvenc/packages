<?php

namespace Obullo\Container;

/**
 * Service Provider Interface
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ServiceProviderInterface
{
    /**
     * Get connection
     *
     * @param array $params array
     *
     * @return object
     */
    public function get($params = array());
}