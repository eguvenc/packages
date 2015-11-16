<?php

namespace Obullo\Http;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Http response Class. Set Http Response Code, Write Outputs, Set & Finalize Headers
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * Zend stratigility support
     * 
     * @var bool
     */
    private $complete = false;

    /**
     * Map of standard HTTP status code/reason phrases
     *
     * @var array
     */
    private $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * Http status code
     * 
     * @var string
     */
    protected $statusCode;

    /**
     * Http status code reason phrase
     * 
     * @var string
     */
    protected $reasonPhrase;

    /**
     * Constructor
     * 
     * @param mixed $body    Stream  identifier and/or actual stream resource
     * @param int   $status  Status  code for the response, if any.
     * @param array $headers Headers for the response, if any.
     * 
     * @throws InvalidArgumentException on any invalid element.
     */
    public function __construct($body = 'php://memory', $status = 200, array $headers = [])
    {
        if (! is_string($body) && ! is_resource($body) && ! $body instanceof StreamInterface) {
            throw new InvalidArgumentException(
                'Stream must be a string stream resource identifier, '
                . 'an actual stream resource, '
                . 'or a Psr\Http\Message\StreamInterface implementation'
            );
        }
        if (null !== $status) {
            $this->validateStatus($status);
        }
        $this->stream     = ($body instanceof StreamInterface) ? $body : new Stream($body, 'wb+');
        $this->statusCode = $status ? (int) $status : 200;

        list($this->headerNames, $headers) = $this->filterHeaders($headers);
        $this->assertHeaders($headers);
        $this->headers = $headers;
    }

    /**
    * Set HTTP Status Header
    * 
    * @param int    $code         the status code
    * @param string $reasonPhrase reason
    * 
    * @return object
    */    
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->validateStatus($code);

        $new = clone $this;
        $new->statusCode   = (int) $code;
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    /**
     * Validate a status code.
     *
     * @param int|string $code code
     * 
     * @throws InvalidArgumentException on an invalid status code.
     */
    private function validateStatus($code)
    {
        if (! is_numeric($code)
            || is_float($code)
            || $code < 100
            || $code >= 600
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid status code "%s"; must be an integer between 100 and 599, inclusive',
                    (is_scalar($code) ? $code : gettype($code))
                )
            );
        }
    }

    /**
     * Returns to response status
     * 
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns http status code reason phrase
     * 
     * @return string
     */
    public function getReasonPhrase()
    {
        if (! $this->reasonPhrase
            && isset($this->phrases[$this->statusCode])
        ) {
            $this->reasonPhrase = $this->phrases[$this->statusCode];
        }
        return $this->reasonPhrase;
    }

    /**
     * Ensure header names and values are valid.
     *
     * @param array $headers headers
     * 
     * @throws InvalidArgumentException
     */
    private function assertHeaders(array $headers)
    {
        foreach ($headers as $name => $headerValues) {
            HeaderSecurity::assertValidName($name);
            array_walk($headerValues, __NAMESPACE__ . '\HeaderSecurity::assertValid');
        }
    }

    //----------- OBULLO METHODS ----------//

    /**
     * Create a JSON response with the given data.
     *
     * Default JSON encoding is performed with the following options, which
     * produces RFC4627-compliant JSON, capable of embedding into HTML.
     * 
     * - JSON_HEX_TAG
     * - JSON_HEX_APOS
     * - JSON_HEX_AMP
     * - JSON_HEX_QUOT
     * 
     * @param array   $data            json data
     * @param integer $status          http status code
     * @param array   $headers         json headers
     * @param integer $encodingOptions json ecoding options
     * 
     * @return object
     */
    public function json(array $data, $status = 200, array $headers = [], $encodingOptions = 15)
    {
        $this->validateStatus($status);
        $json = new \Obullo\Http\Response\JsonResponse($data, $headers, $encodingOptions);

        $this->__construct($json->getBody(), $status, $json->getHeaders());  // Invoke response
        return $this;
    }

    /**
     * Create an HTML response.
     *
     * Produces an HTML response with a Content-Type of text/html and a default
     * status of 200.
     * 
     * @param string  $html    content
     * @param integer $status  status
     * @param array   $headers headers
     * 
     * @return void
     */
    public function html($html, $status = 200, array $headers = [])
    {
        $this->validateStatus($status);
        $html = new \Obullo\Http\Response\HtmlResponse($html, $headers);

        $this->__construct($html->getBody(), $status, $html->getHeaders());  // Invoke response
        return $this;
    }

    /**
     * Redirect response
     * 
     * @param string  $uri     uri
     * @param integer $status  status
     * @param array   $headers headers
     * 
     * @return void
     */
    public function redirect($uri, $status = 301, array $headers = [])
    {
        $this->validateStatus($status);
        $redirect = new \Obullo\Http\Response\RedirectResponse($uri, $headers);

        $this->__construct('php://temp', $status, $redirect->getHeaders());  // Invoke response
        return $this;
    }

    /**
     * Empty response
     * 
     * @param integer $status  status
     * @param array   $headers headers
     * 
     * @return object
     */
    public function emptyContent($status = 204, array $headers = [])
    {
        $this->validateStatus($status);
        $empty = new \Obullo\Http\Response\EmptyResponse($status, $headers);

        $this->__construct($empty->getBody(), $status, $empty->getHeaders());  // Invoke response
        return $this;
    }

}