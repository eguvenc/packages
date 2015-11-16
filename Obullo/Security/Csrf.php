<?php

namespace Obullo\Security;

use Obullo\Log\LoggerInterface;
use Obullo\Config\ConfigInterface;
use Obullo\Session\SessionInterface;

use Psr\Http\Message\RequestInterface;

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
      * Request
      * 
      * @var object
      */
     protected $request;

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
     * @param object $config  ConfigInterface
     * @param object $logger  LoggerInterface
     * @param object $request RequestInterface
     * @param object $session SessionInterface
     * 
     * @return void
     */
    public function __construct(ConfigInterface $config, LoggerInterface $logger, RequestInterface $request, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->request = $request;
        $this->session = $session;

        $this->config = $config['security'];
        $this->refresh = $this->config['csrf']['token']['refresh'];
        $this->tokenName = $this->config['csrf']['token']['name'];
        $this->tokenData = $this->session->get($this->tokenName);
        $this->setCsrfToken();

        $this->logger->channel('security');
        $this->logger->debug('Csrf Class Initialized');
    }

    /**
     * Verify Cross Site Request Forgery Protection
     *
     * @return boolean
     */
    public function verify()
    {
        if ($this->setCsrfToken()) {
            return true;
        }
        if (! isset($_POST[$this->tokenName]) 
            || ! isset($this->tokenData['value'])
            || ($_POST[$this->tokenName] != $this->tokenData['value'])
        ) {
            return false;
        }
        unset($_POST[$this->tokenName]); // We kill this since we're done and we don't want to  polute the _POST array

        $this->logger->channel('security');
        $this->logger->debug('Csrf token verified');
        return true;
    }

    /**
     * Set csrf token if method not POST
     *
     * @return bool
     */
    protected function setCsrfToken()
    {
        if ($this->request->getMethod() !== 'POST') { // If it's not a POST request we will set the CSRF token
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