<?php

namespace Obullo\Captcha;

/**
 * Captcha Result Handler
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CaptchaResult
{
    /**
     * General failure.
     */
    const FAILURE = 0;

    /**
     * Successful process.
     */
    const SUCCESS = 1;

    /**
     * Has been expired the captcha.
     */
    const FAILURE_EXPIRED = -1;

    /**
     * Invalid captcha code.
     */
    const FAILURE_INVALID_CODE = -2;

    /**
     * Captcha data not found.
     */
    const FAILURE_CAPTCHA_NOT_FOUND = -3;

    /**
     * Captcha result code
     *
     * @var int
     */
    protected $code;

    /**
     * Result message
     * 
     * @var array
     */
    protected $messages = array();

    /**
     * Sets the result code and failure message
     *
     * @param int    $code     result code
     * @param string $messages messages
     */
    public function __construct($code, $messages)
    {
        $this->code = (int)$code;
        $this->messages = $messages;
    }

    /**
     * Returns whether the result represents a successful captcha code
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->code > 0) ? true : false;
    }

    /**
     * Get the result code for this captcha code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set custom error code
     * 
     * @param int $code error code
     *
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Set custom error messages
     * 
     * @param string $message message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * Gets all messages
     * 
     * @return array
     */
    public function getArray()
    {
        return array(
            'code' => $this->code,
            'messages' => $this->messages,
        );
    }
}