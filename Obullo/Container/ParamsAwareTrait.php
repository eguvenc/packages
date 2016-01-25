<?php

namespace Obullo\Container;

trait ParamsAwareTrait
{
    /**
     * Parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Set params
     *
     * @param array $params parameters
     * 
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
