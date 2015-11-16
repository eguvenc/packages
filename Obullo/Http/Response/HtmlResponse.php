<?php

namespace Obullo\Http\Response;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

use Obullo\Http\Response;
use Obullo\Http\Stream;

/**
 * HTML response.
 *
 * Allows creating a response by passing an HTML string to the constructor;
 * by default, sets a status code of 200 and sets the Content-Type header to
 * text/html.
 */
class HtmlResponse
{
    use InjectContentTypeTrait;

    /**
     * Http headers
     * 
     * @var array
     */
    protected $headers;

    /**
     * Raw body
     * 
     * @var string
     */
    protected $body;
    
    /**
     * Create an HTML response.
     *
     * Produces an HTML response with a Content-Type of text/html and a default
     * status of 200.
     * 
     * @param string|StreamInterface $html    content
     * @param array                  $headers headers
     * 
     * @return void
     */
    public function __construct($html, array $headers = [])
    {
        $this->body = $this->createBody($html);
        $this->headers = $this->injectContentType('text/html', $headers);
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
     * Create the message body.
     *
     * @param string|StreamInterface $html
     * @return StreamInterface
     * @throws InvalidArgumentException if $html is neither a string or stream.
     */
    private function createBody($html)
    {
        if ($html instanceof StreamInterface) {
            return $html;
        }

        if (! is_string($html)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid content (%s) provided to %s',
                (is_object($html) ? get_class($html) : gettype($html)),
                __CLASS__
            ));
        }

        $body = new Stream('php://temp', 'wb+');
        $body->write($html);
        return $body;
    }
}
