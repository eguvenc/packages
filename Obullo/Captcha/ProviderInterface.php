<?php

namespace Obullo\Captcha;

/**
 * Captcha Provider Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ProviderInterface
{
    /**
     * Initialize
     * 
     * @return void
     */
    public function init();

    /**
     * Print captcha element js
     * 
     * @return string
     */
    public function printJs();

    /**
     * Print captcha html element
     * 
     * @return string
     */
    public function printHtml();

    /**
     * Get captcha results
     * 
     * @param string $code captcha code
     * 
     * @return boolean
     */
    public function result($code);
}