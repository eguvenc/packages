<?php

namespace Obullo\Security;

use Obullo\Log\LoggerInterface as Logger;
use Obullo\Session\SessionInterface as Session;

use Psr\Http\Message\RequestInterface as Request;

/**
 * ABOUT CSRF
 * 
 * @see http://shiflett.org/articles/cross-site-request-forgeries
 * @see http://blog.beheist.com/csrf-protection-in-codeigniter-2-0-a-closer-look/
 */

/**
 * Csrf Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf 
{
     /**
      * Config
      * 
      * @var array
      */
     protected $config;

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
        if ($this->setCsrfToken($request)) {
            return true;
        }
        $post = $request->getParsedBody();

        if (! isset($post[$this->tokenName]) 
            || ! isset($this->tokenData['value'])
            || ($post[$this->tokenName] != $this->tokenData['value'])
        ) {
            return false;
        }
        $this->logger->channel('security');
        $this->logger->debug('Csrf token verified');
        return true;
    }

    /**
     * Set csrf token if method not POST
     *
     * @param Request $request request
     *
     * @return bool
     */
    public function setCsrfToken(Request $request)
    {
        if ($request->getMethod() !== 'POST') { // If it's not a POST request we will set the CSRF token
            $this->setSession();     // Set token to session if we have empty data
            return true;
        }
        return false;
    }

    /**
     * Set Cross Site Request Forgery Protection Cookie
     *
     * @return object
     */
    protected function setSession()
    {
        if (empty($this->tokenData['value'])) {

            $this->tokenData = [
                'value' => $this->generateHash(),
                'time' => time()
            ];
            $this->session->set($this->tokenName, $this->tokenData);

            $this->logger->channel('security');
            $this->logger->debug('Csrf token session set');
        }
        $this->refreshToken();
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
        $tokenRefresh = strtotime('- '.$this->refresh.' seconds'); // Create a old time belonging to refresh seconds.

        if (isset($this->tokenData['time']) && $tokenRefresh > $this->tokenData['time']) {  // Refresh token
            $this->tokenData = array();  // Reset data for update the token
            $this->setSession();
        }
        return $this->getToken();
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
     * Set Cross Site Request Forgery Protection Cookie
     * 
     * @return string
     */
    protected function generateHash()
    {
        return md5(uniqid(rand(), true));
    }

}