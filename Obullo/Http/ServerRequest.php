<?php

namespace Obullo\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

use Obullo\Filters\InputFilter;
use League\Container\ContainerAwareTrait;
use Interop\Container\ContainerInterface as Container;

/**
 * Borrowed from Zend Diactoros
 */

/**
 * Server-side HTTP request
 *
 * Extends the Request definition to add methods for accessing incoming data,
 * specifically server parameters, cookies, matched path parameters, query
 * string arguments, body parameters, and upload file information.
 *
 * "Attributes" are discovered via decomposing the request (and usually
 * specifically the URI path), and typically will be injected by the application.
 *
 * Requests are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 */
class ServerRequest implements ServerRequestInterface
{
    use MessageTrait, RequestTrait, ContainerAwareTrait;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $cookieParams = [];

    /**
     * @var null|array|object
     */
    private $parsedBody;

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var array
     */
    private $serverParams;

    /**
     * @var array
     */
    private $uploadedFiles;

    /**
     * @param array $serverParams Server parameters, typically from $server
     * @param array $uploadedFiles Upload file information, a tree of UploadedFiles
     * @param null|string $uri URI for the request, if any.
     * @param null|string $method HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body Message body, if any.
     * @param array $headers Headers for the message, if any.
     * @throws InvalidArgumentException for any invalid value.
     */
    public function __construct(
        array $serverParams = [],
        array $uploadedFiles = [],
        $uri = null,
        $method = null,
        $body = 'php://input',
        array $headers = []
    ) {
        $this->validateUploadedFiles($uploadedFiles);

        $this->initialize($uri, $method, $body, $headers);
        $this->serverParams  = $serverParams;
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        $headers = $this->headers;
        if (! $this->hasHeader('host')
            && ($this->uri && $this->uri->getHost())
        ) {
            $headers['Host'] = [$this->getHostFromUri()];
        }
        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        if (! $this->hasHeader($header)) {
            if (strtolower($header) === 'host'
                && ($this->uri && $this->uri->getHost())
            ) {
                return [$this->getHostFromUri()];
            }
            return [];
        }
        $header = $this->headerNames[strtolower($header)];
        $value  = $this->headers[$header];
        $value  = is_array($value) ? $value : [$value];
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->validateUploadedFiles($uploadedFiles);
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only.
     * 
     * @see https://github.com/php-fig/fig-standards/issues/507
     * 
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     * @throws RuntimeException if the request body media type parser returns an invalid value
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute, $default = null)
    {
        if (! array_key_exists($attribute, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$attribute];
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($attribute)
    {
        if (! isset($this->attributes[$attribute])) {
            return clone $this;
        }
        $new = clone $this;
        unset($new->attributes[$attribute]);
        return $new;
    }

    /**
     * Proxy to receive the request method.
     *
     * This overrides the parent functionality to ensure the method is never
     * empty; if no method is present, it returns 'GET'.
     *
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->method)) {
            return 'GET';
        }
        return $this->method;
    }

    /**
     * Set the request method.
     *
     * Unlike the regular Request implementation, the server-side
     * normalizes the method to uppercase to ensure consistency
     * and make checking the method simpler.
     *
     * This methods returns a new instance.
     *
     * @param string $method method
     * 
     * @return self
     */
    public function withMethod($method)
    {
        $this->validateMethod($method);
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    /**
     * Set the body stream
     *
     * @param string|resource|StreamInterface $stream stream
     * 
     * @return StreamInterface
     */
    private function getStream($stream)
    {
        if ($stream === 'php://input') {
            return new PhpInputStream();
        }
        if (! is_string($stream) && ! is_resource($stream) && ! $stream instanceof StreamInterface) {
            throw new InvalidArgumentException(
                'Stream must be a string stream resource identifier, '
                . 'an actual stream resource, '
                . 'or a Psr\Http\Message\StreamInterface implementation'
            );
        }
        if (! $stream instanceof StreamInterface) {
            return new Stream($stream, 'r');
        }
        return $stream;
    }

    /**
     * Recursively validate the structure in an uploaded files array.
     *
     * @param array $uploadedFiles $_FILES
     * 
     * @throws InvalidArgumentException if any leaf is not an UploadedFileInterface instance.
     */
    private function validateUploadedFiles(array $uploadedFiles)
    {
        foreach ($uploadedFiles as $file) {
            if (is_array($file)) {
                $this->validateUploadedFiles($file);
                continue;
            }

            if (! $file instanceof UploadedFileInterface) {
                throw new InvalidArgumentException('Invalid leaf in uploaded files structure');
            }
        }
    }

    //----------------- OBULLO METHODS -------------------//

    /**
     * GET wrapper
     * 
     * @param string|null $key    key
     * @param boolean     $filter name
     * 
     * @return mixed
     */
    public function get($key = null, $filter = null)
    {
        $get = $this->getQueryParams();

        if (is_null($key)) {
            return $get;
        }
        $value = isset($get[$key]) ? $get[$key] : false;
        if (is_string($filter)) {
            $inputFilter = new InputFilter;
            $inputFilter->setContainer($this->container);
            return $inputFilter->setFilter($filter)->setValue($value);
        }
        return $value;
    }

    /**
     * POST wrapper
     * 
     * @param string|null $key    key
     * @param boolean     $filter name
     * 
     * @return mixed
     */
    public function post($key = null, $filter = null)
    {
        $post = $this->getParsedBody();

        if (is_null($key)) {
            return $post;
        }
        $value = isset($post[$key]) ? $post[$key] : false;

        if (is_string($filter)) {
            $inputFilter = new InputFilter;
            $inputFilter->setContainer($this->container);
            return $inputFilter->setFilter($filter)->setValue($value);
        }
        return $value;
    }

    /**
     * REQUEST wrapper
     * 
     * @param string|null $key    key
     * @param boolean     $filter name
     * 
     * @return mixed
     */
    public function all($key = null, $filter = null)
    {
        $get = $this->get();
        $post = $this->post();
        $request = array_merge($post, $get);

        if (is_null($key)) {
            return $request;
        }
        $value = isset($request[$key]) ? $request[$key] : false;

        if (is_string($filter)) {
            $inputFilter = new InputFilter;
            $inputFilter->setContainer($this->container);
            return $inputFilter->setFilter($filter)->setValue($value);
        }
        return $value;
    }

    /**
     * Get $server variable items
     * 
     * @param string|null $key server key
     * 
     * @return void
     */
    public function server($key = null) 
    {
        $server = $this->getServerParams();
        
        if (is_null($key)) {
            return $server;
        }
        if (isset($server[$key])) {
            return $server[$key];
        }
        return null;
    }

    /**
     * Get ip address
     * 
     * @return string
     */
    public function getIpAddress()
    {
        static $ipAddress = '';
        $ipAddress = $this->getAttribute('TRUSTED_IP');

        if (empty($ipAddress)) {
            $server = $this->getServerParams();
            $ipAddress = isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '0.0.0.0';
        }
        return $ipAddress;
    }
    
    /**
     * Detect layer (HMVC) requests
     * 
     * @return boolean
     */
    public function isLayer()
    {
        $server = $this->getServerParams();

        if (isset($server['LAYER_REQUEST']) && $server['LAYER_REQUEST'] == true) {
            return true;
        }
        return false;
    }

    /**
     * Detect the request is console
     * 
     * @return boolean
     */
    public function isCli()
    {
        return (PHP_SAPI === 'cli' || defined('STDIN'));
    }

    /**
     * Detect the request is xmlHttp ( Ajax )
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Detect the connection is secure ( Https )
     * 
     * @return boolean
     */
    public function isSecure()
    {
        $server = $this->getServerParams();

        if (! empty($server['HTTPS']) && strtolower($server['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (! empty($server['HTTP_FRONT_END_HTTPS']) && strtolower($server['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * If http request type equal to POST returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * If http request type equal to GET returns true otherwise false.
     * 
     * @return boolean
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * If http request type equal to PUT returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * If http request type equal to PATCH returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Check method is head
     * 
     * @return boolean
     */
    public function isHead()
    {
        return $this->isMethod('HEAD');
    }

    /**
     * Check method is options
     * 
     * @return boolean
     */
    public function isOptions()
    {
        return $this->isMethod('OPTIONS');
    }

    /**
     * If http request type equal to DELETE returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * Does this request use a given method?
     *
     * @param string $method HTTP method
     * 
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

}
