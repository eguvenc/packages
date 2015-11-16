<?php

namespace Obullo\Translation;

use ArrayAccess;

/**
 * Translator Interface
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface TranslatorInterface extends ArrayAccess
{
    /**
     * Load a translation file
     *
     * @param string $filename filename
     * 
     * @return object translator
     */
    public function load($filename);

    /**
     * Gets a parameter or an object.
     *
     * @return mixed the value of the parameter or an object
     */
    public function get();

    /**
     * Get translator class cookie
     *
     * @return string
     */
    public function getCookie();

    /**
     * Set locale name
     *
     * @param string  $locale      language ( en, es )
     * @param boolean $writeCookie write cookie on / off
     *
     * @return boolean
     */
    public function setLocale($locale = null, $writeCookie = true);

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Sets default locale
     *
     * @param string $locale ( en, de, tr .. )
     *
     * @return void
     */
    public function setDefault($locale);

    /**
     * Returns to default locale ( en )
     *
     * @return string
     */
    public function getDefault();

    /**
     * Set the fallback locale being used.
     *
     * @return string
     */
    public function getFallback();

    /**
     * Set the fallback locale being used.
     *
     * @param string $fallback locale name
     *
     * @return void
     */
    public function setFallback($fallback);

    /**
     * Write to cookies
     *
     * @return void
     */
    public function setCookie();
}