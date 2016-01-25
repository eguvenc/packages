<?php

namespace Obullo\Container;

/**
 * Inject parameters
 */
interface ParamsAwareInterface
{
    /**
     * Inject controller object
     * 
     * @param array $params parameters
     * 
     * @return void
     */
    public function setParams(array $params);

    /**
     * Get parameters
     * 
     * @return array
     */
    public function getParams();
}
