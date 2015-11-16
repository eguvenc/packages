<?php

namespace Obullo\Http\Response;

use Psr\Http\Message\UriInterface as Uri;
use InvalidArgumentException;

/**
 * Produce a redirect response.
 */
class RedirectResponse
{
    /**
     * Http headers
     * 
     * @var array
     */
    protected $headers;

    /**
     * Create a redirect response.
     *
     * Produces a redirect response with a Location header and the given status
     * (302 by default).
     *
     * Note: this method overwrites the `location` $headers value.
     *
     * @param string|UriInterface $uri     URI for the Location header.
     * @param array               $headers Array of headers to use at initialization.
     */
    public function __construct($uri,  array $headers = [])
    {
        if (! is_string($uri) && ! $uri instanceof Uri) {
            throw new InvalidArgumentException(
                sprintf(
                    'Uri provided to %s MUST be a string or Psr\Http\Message\UriInterface instance; received "%s"',
                    __CLASS__,
                    (is_object($uri) ? get_class($uri) : gettype($uri))
                )
            );
        }
        $uri = (string) $uri;
        $headers['location'] = [$uri];
        $this->headers = $headers;
    }

    /**
     * Returns to redirect headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
