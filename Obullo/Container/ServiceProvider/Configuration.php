<?php

namespace Obullo\Container\ServiceProvider;

use League\Container\Argument\RawArgument;

/**
 * Service provider configuration
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Configuration
{
    /**
     * Service params
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param array $parameters service parameters
     */
    public function __construct(array $parameters)
    {
        $this->params = $parameters;
    }

    /**
     * Returns to service params
     * 
     * @return array|false
     */
    public function getParams()
    {
        if (isset($this->params['params'])) {
            return $this->params['params'];
        }
        return false;
    }

    /**
     * Returns to service methods
     * 
     * @return array|false
     */
    public function getMethods()
    {
        if (isset($this->params['methods'])) {

            foreach ($this->params['methods'] as $key => $method) {
                
                foreach ($method['argument'] as $k => $v) {
                    if (is_string($v)) {
                        $method['argument'][$k] = new RawArgument($v);  // Convert strings to raw objects.
                    }
                }
                $this->params['methods'][$key] = $method;
            }
            return $this->params['methods'];
        }
        return false;
    }
    
}
