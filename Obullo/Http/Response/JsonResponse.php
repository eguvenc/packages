<?php

namespace Obullo\Http\Response;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

use Obullo\Http\Response;
use Obullo\Http\Stream;

/**
 * JSON response.
 *
 * Allows creating a response by passing data to the constructor; by default,
 * serializes the data to JSON, sets a status code of 200 and sets the
 * Content-Type header to application/json.
 */
class JsonResponse
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
     * @param array   $headers         json headers
     * @param integer $encodingOptions json ecoding options
     * 
     * @return void
     */
    public function __construct($data, array $headers = [], $encodingOptions = 15)
    {
        $body = new Stream('php://temp', 'wb+');
        $body->write($this->jsonEncode($data, $encodingOptions));

        $this->body = $body;
        $this->headers = $this->injectContentType('application/json', $headers);
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
     * Encode the provided data to JSON.
     *
     * @param mixed $data
     * @param int $encodingOptions
     * @return string
     * @throws InvalidArgumentException if unable to encode the $data to JSON.
     */
    private function jsonEncode($data, $encodingOptions)
    {
        if (is_resource($data)) {
            throw new InvalidArgumentException('Cannot JSON encode resources');
        }

        // Clear json_last_error()
        json_encode(null);

        $json = json_encode($data, $encodingOptions);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'Unable to encode data to JSON in %s: %s',
                __CLASS__,
                json_last_error_msg()
            ));
        }

        return $json;
    }
}
