<?php

namespace Obullo\Http\Response;

trait InjectContentTypeTrait
{
    /**
     * Inject the provided Content-Type, if none is already present.
     *
     * @param string $contentType content type
     * @param array  $headers     headers
     * 
     * @return array Headers with injected Content-Type
     */
    private function injectContentType($contentType, array $headers)
    {
        $hasContentType = array_reduce(
            array_keys($headers), 
            function ($carry, $item) {
                return $carry ?: (strtolower($item) === 'content-type');
            },
            false
        );

        if (! $hasContentType) {
            $headers['content-type'] = [$contentType];
        }
        return $headers;
    }
    
}
