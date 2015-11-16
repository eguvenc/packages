<?php

namespace Obullo\Http\Response;

use Obullo\Http\Response;
use Obullo\Http\Stream;

/**
 * A class representing empty HTTP responses.
 */
class EmptyResponse
{
    /**
     * Body
     * 
     * @var resource
     */
    protected $body;

    /**
     * Http headers
     * 
     * @var array
     */
    protected $headers;

    /**
     * Create an empty response with the given status code.
     *
     * @param int   $status  Status code for the response, if any.
     * @param array $headers Headers for the response, if any.
     */
    public function __construct($status = 204, array $headers = [])
    {
        $body = new Stream('php://temp', 'r');

        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Returns to json headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns to raw body
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Create an empty response with the given headers.
     *
     * @param array $headers Headers for the response.
     * 
     * @return EmptyResponse
     */
    public function withHeaders(array $headers)
    {
        $this->__construct(204, $headers);

        return $this;
    }
}
