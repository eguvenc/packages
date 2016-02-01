<?php

namespace Obullo\Config;

trait ConfigAwareTrait
{
    /**
     * Config
     * 
     * @var array
     */
    protected $config;

    /**
     * Set config
     *
     * @param object $config config
     * 
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
