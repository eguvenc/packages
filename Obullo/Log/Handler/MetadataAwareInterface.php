<?php

namespace Obullo\Log\Handler;

/**
 * Metadata Aware Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface MetadataAwareInterface
{
    /**
     * Set log event meta data 
     *
     * @param array $meta data
     * 
     * @return void
     */
    public function setMetadata(array $meta);

    /**
     * Returns to metadata
     * 
     * @return array
     */
    public function getMetadata();
}