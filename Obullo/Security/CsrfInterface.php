<?php

namespace Obullo\Security;

use Psr\Http\Message\RequestInterface as Request;

/**
 * Csrf Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface CsrfInterface
{
    /**
     * Verify Cross Site Request Forgery Protection
     *
     * @param Request $request request
     * 
     * @return boolean
     */
    public function verify(Request $request);

    /**
     * Get CSRF Hash
     *
     * Getter Method
     *
     * @return string
     */
    public function getToken();

    /**
     * Get CSRF Token Name
     *
     * Getter Method
     *
     * @return string csrf token name
     */
    public function getTokenName();

    /**
     * Salt for CSRF token
     *
     * @param string $salt salt
     * 
     * @return void
     */
    public function setSalt($salt = '');

    /**
     * Set csrf error
     * 
     * @param string $error message
     * @param int    $code  error code
     *
     * @return void
     */
    public function setError($error, $code = 00);

    /**
     * Returns to csrf error
     * 
     * @return string
     */
    public function getError();

    /**
     * Returns to last error code
     * 
     * @return int
     */
    public function getErrorCode();

}