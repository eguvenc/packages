<?php

namespace Obullo\Log\Handler;

trait MetadataAwareTrait
{
    /**
     * Metadata
     * 
     * @var array
     */
    protected $metadata;

    /**
     * Set metadata
     *
     * @param array $metadata logger metadata
     * 
     * @return $this
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
