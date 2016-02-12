<?php

namespace Obullo\Captcha\Provider;

use Obullo\Captcha\CaptchaResult;
use Obullo\Captcha\AbstractProvider;
use Obullo\Captcha\CaptchaInterface;

use Obullo\Log\LoggerInterface as Logger;
use Psr\Http\Message\RequestInterface as Request;
use Obullo\Translation\TranslatorInterface as Translator;

/**
 * Google ReCaptcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptcha extends AbstractProvider implements CaptchaInterface
{
    /**
     * Api data
     */
    const CLIENT_API = 'https://www.google.com/recaptcha/api.js';
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Error codes constants
     */
    const FAILURE_MISSING_INPUT_SECRET   = 'missing-input-secret';
    const FAILURE_INVALID_INPUT_SECRET   = 'invalid-input-secret';
    const FAILURE_MISSING_INPUT_RESPONSE = 'missing-input-response';
    const FAILURE_INVALID_INPUT_RESPONSE = 'invalid-input-response';

    /**
     * Current language
     * 
     * @var string
     * @see https://developers.google.com/recaptcha/docs/language
     */
    protected $lang;

    /**
     * Captcha html
     * 
     * @var string
     */
    protected $html;

    /**
     * The user's IP address. (optional)
     * 
     * @var string
     */
    protected $userIp;

    /**
     * Translator
     * 
     * @var object
     */
    protected $translator;

    /**
     * Error codes
     * 
     * @var array
     * @see https://developers.google.com/recaptcha/docs/verify
     */
    protected $errorCodes = array();

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params = array();

    /**
     * Validation callback
     * 
     * @var boolean
     */
    protected $validation = false;

    /**
     * Constructor
     * 
     * @param object $request    \Psr\Http\Message\RequestInterface
     * @param object $translator \Obullo\Translation\TranslatorInterface
     * @param object $logger     \Obullo\Log\LoggerInterface
     * @param array  $params     service parameters
     */
    public function __construct(
        Request $request,
        Translator $translator,
        Logger $logger,
        array $params
    ) {
        $this->params = $params;
        $this->request = $request;
        $this->translator = $translator;
        $this->logger = $logger;

        $this->errorCodes = array(
            self::FAILURE_MISSING_INPUT_SECRET   => $this->translator['OBULLO:VALIDATOR:RECAPTCHA:MISSING_INPUT_SECRET'],
            self::FAILURE_INVALID_INPUT_SECRET   => $this->translator['OBULLO:VALIDATOR:RECAPTCHA:INVALID_INPUT_SECRET'],
            self::FAILURE_MISSING_INPUT_RESPONSE => $this->translator['OBULLO:VALIDATOR:RECAPTCHA:MISSING_INPUT_RESPONSE'],
            self::FAILURE_INVALID_INPUT_RESPONSE => $this->translator['OBULLO:VALIDATOR:RECAPTCHA:INVALID_INPUT_RESPONSE']
        );
        $this->init();
        $this->logger->debug('ReCaptcha Class Initialized');
    }

    /**
     * Initialize
     * 
     * @return void
     */
    public function init()
    {
        if ($this->params['user']['autoSendIp']) {
            $this->setUserIp($this->request->getIpAddress());
        }
        $this->buildHtml();
    }

    /**
     * Set site key
     * 
     * @param string $siteKey site key
     * 
     * @return self object
     */
    public function setSiteKey($siteKey)
    {
        $this->params['api']['key']['site'] = $siteKey;
        return $this;
    }

    /**
     * Set secret key
     * 
     * @param string $secretKey secret key
     * 
     * @return self object
     */
    public function setSecretKey($secretKey)
    {
        $this->params['api']['key']['secret'] = $secretKey;
        return $this;
    }

    /**
     * Set api language
     * 
     * @param string $lang language
     * 
     * @return self object
     */
    public function setLang($lang)
    {
        $this->params['locale']['lang'] = $lang;
        return $this;
    }

    /**
     * Set remote user ip address (optional)
     * 
     * @param string $ip user ip address
     * 
     * @return self object
     */
    public function setUserIp($ip)
    {
        $this->userIp = $ip;
        return $this;
    }

    /**
     * Get user ip
     * 
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * Get site key
     * 
     * @return string
     */
    public function getSiteKey()
    {
        return $this->params['api']['key']['site'];
    }

    /**
     * Get secret key
     * 
     * @return string
     */
    public function getSecretKey()
    {
        return $this->params['api']['key']['secret'];
    }

    /**
     * Get api language
     * 
     * @return string
     */
    public function getLang()
    {
        return $this->params['locale']['lang'];
    }

    /**
     * Get captcha input name
     * 
     * @return string name
     */
    public function getInputName()
    {
        return $this->params['form']['input']['attributes']['name'];
    }

    /**
     * Get javascript link
     * 
     * @return string
     */
    public function printJs()
    {
        $lang = $this->getLang();
        $link = static::CLIENT_API;

        if (! empty($lang)) {
            $link = static::CLIENT_API .'?hl='. $lang;
        }
        return '<script src="'.$link.'" async defer></script>';
    }

    /**
     * Print captcha html
     * 
     * @return string html
     */
    public function printHtml()
    {
        return $this->html;
    }

    /**
     * Disabled
     * 
     * @return string
     */
    public function printRefreshButton()
    {
        return;
    }

    /**
     * Validation captcha
     * 
     * @param string $code result
     * 
     * @return bool
     */
    public function result($code)
    {
        $response = $this->sendVerifyRequest(
            array(
                'secret'   => $this->getSecretKey(),
                'response' => $code,
                'remoteip' => $this->getUserIp() // optional
            )
        );
        return $this->validateCode($response);
    }

    /**
     * Validate captcha
     * 
     * @param array $response response
     * 
     * @return Captcha\CaptchaResult object
     */
    protected function validateCode($response)
    {
        if (isset($response['success'])) {
            if (! $response['success']
                && isset($response['error-codes'])
                && sizeof($response['error-codes']) > 0
            ) {
                foreach ($response['error-codes'] as $err) {
                    if (isset($this->errorCodes[$err])) {
                        $this->result['code'] = $err;
                        $this->result['messages'][] = $this->errorCodes[$err];
                    }
                }
                return $this->createResult();
            }
            if ($response['success'] === true) {
                $this->result['code'] = CaptchaResult::SUCCESS;
                $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:SUCCESS'];
                return $this->createResult();
            }
        }
        $this->result['code'] = CaptchaResult::FAILURE_CAPTCHA_NOT_FOUND;
        $this->result['messages'][] = $this->translator['OBULLO:VALIDATOR:CAPTCHA:NOT_FOUND'];
        return $this->createResult();
    }

    /**
     * Send request verify
     * 
     * @param array $query http query
     * 
     * @return array
     */
    protected function sendVerifyRequest(array $query = array())
    {
        $link = static::VERIFY_URL .'?'. http_build_query($query);
        $response = file_get_contents($link);
        return json_decode($response, true);
    }

    /**
     * Build html
     * 
     * @return void
     */
    protected function buildHtml()
    {
        foreach ($this->params['form'] as $key => $val) {
            $this->html .= vsprintf(
                '<%s %s/>',
                array(
                    $key,
                    $this->buildAttributes($val['attributes'])
                )
            );
        }
        $attributes['data-sitekey'] = $this->getSitekey();
        $this->html.= sprintf(
            '<div class="g-recaptcha" %s></div>',
            $this->buildAttributes($attributes)
        );
    }

    /**
     * Build attributes
     * 
     * @param array $attributes attributes
     * 
     * @return string
     */
    protected function buildAttributes(array $attributes)
    {
        $html = array();
        foreach ($attributes as $key => $value) {
            $html[] = $key.'="'.$value.'"';
        }
        return count($html) ? implode(' ', $html) : '';
    }
    
}