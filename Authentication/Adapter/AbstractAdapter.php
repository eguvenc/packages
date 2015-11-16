<?php

namespace Obullo\Authentication\Adapter;

/**
 * Abstract Adapater
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Regenerate the session id
     *
     * @param bool $deleteOldSession whether to delete old session id
     * 
     * @return void
     */
    public function regenerateSessionId($deleteOldSession = true)
    {
        return $this->session->regenerateId($deleteOldSession);
    }

    /**
     * Verify password hash
     * 
     * @param string $plain plain  password
     * @param string $hash  hashed password
     * 
     * @return boolean | array
     */
    public function verifyPassword($plain, $hash)
    {
        $cost = $this->params['security']['passwordNeedsRehash']['cost'];

        if (password_verify($plain, $hash)) {

            if (password_needs_rehash($hash, PASSWORD_BCRYPT, array('cost' => $cost))) {

                $value = password_hash($plain, PASSWORD_BCRYPT, array('cost' => $cost));

                return array('hash' => $value);
            }
            return true;
        }
        return false;
    }

    /**
     * This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @return bool|Obullo\Authentication\Result 
     */
    abstract protected function validateResultSet();

    /**
     * This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * identity provided to this adapter.
     *
     * @return AuthResult
     */
    abstract protected function validateResult();
}