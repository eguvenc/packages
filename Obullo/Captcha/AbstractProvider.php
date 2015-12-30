<?php

namespace Obullo\Captcha;

use Obullo\Captcha\CaptchaResult;

/**
 * Captcha Abstract Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractProvider
{
    /**
     * Result
     * 
     * @var array
     */
    public $result = array(
        'code' => '',
        'messages' => [],
    );

    /**
     * Initialize
     * 
     * @return void
     */
    abstract public function init();

    /**
     * Print javascript link
     * 
     * @return string
     */
    abstract public function printJs();

    /**
     * Print captcha html element
     * 
     * @return string
     */
    abstract public function printHtml();

    /**
     * Check captcha code
     * 
     * @param string|null $code captcha code
     * 
     * @return boolean
     */
    abstract public function result($code);
    
    /**
     * Create result.
     * 
     * @return CaptchaResult object
     */
    protected function createResult()
    {
        return new CaptchaResult(
            $this->result['code'],
            $this->result['messages']
        );
    }
}