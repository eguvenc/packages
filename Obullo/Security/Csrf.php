<?php

namespace Obullo\Security;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Session\SessionInterface as Session;

use Obullo\Utils\Random;
use Psr\Http\Message\RequestInterface as Request;

/**
 * Csrf Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * 
 * @see http://shiflett.org/articles/cross-site-request-forgeries
 * @see http://blog.beheist.com/csrf-protection-in-codeigniter-2-0-a-closer-look/
 */
class Csrf implements CsrfInterface
{
     /**
      * Token salt
      *
      * @var string
      */
     protected $salt;

     /**
      * Error code
      * 
      * @var int
      */
     protected $code;

     /**
      * Error message
      * 
      * @var string
      */
     protected $error;
    
     /**
      * Service parameters
      * 
      * @var array
      */
     protected $params;

     /**
      * Logger
      * 
      * @var object
      */
     protected $logger;

     /**
      * Session class
      * 
      * @var object
      */
     protected $session;

     /**
      * Token refresh seconds
      * 
      * @var integer
      */
     protected $refresh;

     /**
      * Token session data
      * 
      * @var array | false
      */
     protected $tokenData;

     /**
     * Token name for Cross Site Request Forgery Protection
     *
     * @var string
     */
     protected $tokenName = 'csrf_token';

    /**
     * Constructor
     * 
     * @param object $session SessionInterface
     * @param object $logger  LoggerInterface
     * @param array  $params  service parameters
     * 
     * @return void
     */
    public function __construct(Session $session, Logger $logger, array $params)
    {
        $this->logger  = $logger;
        $this->params  = $params;
        $this->session = $session;

        $this->refresh   = $this->params['token']['refresh'];
        $this->tokenName = $this->params['token']['name'];
        $this->tokenData = $this->session->get($this->tokenName);

        $this->setSalt();

        $this->logger->channel('security');
        $this->logger->debug('Csrf Class Initialized');
    }

    /**
     * Verify Cross Site Request Forgery Protection
     *
     * @param Request $request request
     * 
     * @return boolean
     */
    public function verify(Request $request)
    {
        if ($this->params['protection'] == false) {
            return true;
        }
        $post = $request->getParsedBody();

        if (! isset($post[$this->tokenName])) {
            $this->setError('The csrf token does not exist in post data.', 00);
            return false;
        }
        if (! isset($this->tokenData['value'])
            || ($post[$this->tokenName] != $this->tokenData['value'])
        ) {
            $this->setError('The csrf verfication is failed.', 01);
            return false;
        }
        return true;
    }

    /**
     * Set csrf error
     * 
     * @param string $error message
     * @param string $code  error code
     *
     * @return void
     */
    public function setError($error, $code = 00)
    {
        $this->error = (string)$error;
        $this->code  = $code;
    }

    /**
     * Returns to csrf error
     * 
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns to last error code
     * 
     * @return int
     */
    public function getErrorCode()
    {
        return $this->code;
    }

    /**
     * Get CSRF Hash
     *
     * Getter Method
     *
     * @return string
     */
    public function getToken()
    {
        $this->setToken();
        $this->refreshToken();

        return $this->tokenData['value'];
    }

    /**
     * Get CSRF Token Name
     *
     * Getter Method
     *
     * @return string csrf token name
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * Salt for CSRF token
     *
     * @param string $salt salt
     * 
     * @return void
     */
    public function setSalt($salt = '')
    {
        if (empty($salt)) {
            $salt = $this->params['token']['salt'];
        }
        $this->salt = (string) $salt;
    }

    /**
     * Retrieve salt for CSRF token
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set Csrf cookie if not available in session
     *
     * @return object
     */
    protected function setToken()
    {
        if (empty($this->tokenData['value'])) {
            $this->createToken();
        }
        return $this;
    }

    /**
     * Check csrf time every "x" seconds and update the
     * session if token expired.
     * 
     * @return void
     */
    protected function refreshToken()
    {
        /**
         * Create a refresh time
         */
        $tokenRefresh = strtotime('- '.$this->refresh.' seconds');

        if (isset($this->tokenData['time']) && $tokenRefresh > $this->tokenData['time']) {
            $this->createToken();
        }
    }

    /**
     * Create new token in session
     * 
     * @return void
     */
    protected function createToken()
    {
        $this->tokenData = [
                'value' => $this->generateHash(),
                'time' => time()
            ];
        $this->session->set($this->tokenName, $this->tokenData);
    }

    /**
     * Set Cross Site Request Forgery Protection Cookie
     * 
     * @return string
     */
    protected function generateHash()
    {
        return md5($this->getSalt() . Random::generate('alnum', 32) . uniqid(rand(), true));
    }

}