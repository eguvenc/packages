<?php

namespace Obullo\Application\Middleware;

trait SetDefaultLocaleTrait
{
    /**
     * Translator config
     * 
     * @var array
     */
    public $config;

    /**
     * Locale cookie
     * 
     * @var string
     */
    public $cookie;

    /**
     * Set default locale
     * 
     * @return void
     */
    public function setLocale()
    {
        if (defined('STDIN')) { // Disable console & task errors
            return;
        }
        $this->config = $this->c['config']->load('translator'); 
        $this->cookie = $this->translator->getCookie();

        if ($this->setByUri()) {  // Sets using http://example.com/en/welcome first segment of uri
            return;
        }
        if ($this->setByOldCookie()) {   // Sets by reading old cookie 
            return;
        }
        if ($this->setByBrowserDefault()) {  // Sets by detecting browser language using intl extension  
            return;
        }
        $this->setDefault();  // Set using default language which is configured in translator config
    }


    /**
     * Set using uri http GET request
     *
     * @return bool
     */
    public function setByUri()
    {
        if ($this->config['uri']['segment']) {
            $segment = $this->uri->segment($this->config['uri']['segmentNumber']);  // Set via URI Segment
            if (! empty($segment)) {
                $bool = ($this->cookie == $segment) ? false : true; // Do not write if cookie == segment value same
                if ($this->translator->setLocale($segment, $bool)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Set using browser old cookie
     *
     * @return bool
     */
    public function setByOldCookie()
    {       
        if (! empty($this->cookie)) {                               // If we have a cookie then set locale using cookie.
            $this->translator->setLocale($this->cookie, false); // Do not write to cookie just set variables.
            return true;
        }
        return false;
    }

    /**
     * Set using php intl extension
     *
     * @return bool
     */
    public function setByBrowserDefault()
    {
        $intl = extension_loaded('intl');     // Intl extension should be enabled.

        if ($intl == false) {
            $this->logger->notice('Install php intl extension to enable detecting browser language feature.');
            return false;
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $intl) {   // Set via browser default value
            $default = strstr(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']), '_', true);
            $this->translator->setLocale($default);
            return true;
        }
        return false;
    }

    /**
     * Set using alternative default language
     *
     * @return void
     */
    public function setDefault()
    {
        $this->translator->setLocale($this->translator->getDefault());
    }

}