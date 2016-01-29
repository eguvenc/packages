<?php

namespace Obullo\Validator\Rules;

use Obullo\Security\CsrfInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Validator\FieldInterface as Field;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Csrf form verify
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf extends AbstractRule implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Check csrf code
     * 
     * @return boolean
     */
    public function isValid()
    {
        $field     = $this->getField();
        $container = $this->getContainer();
        $request   = $container->get('request');

        if ($request->getMethod() == 'POST') {

            $csrf = $container->get('csrf');

            if (! $csrf->verify($request)) {

                $code = $csrf->getErrorCode();
                
                switch ($code) {
                case 00:
                    $field->setMessage('OBULLO:VALIDATOR:CSRF:REQUIRED');
                    break;
                case 01:
                    $field->setMessage('OBULLO:VALIDATOR:CSRF:INVALID');
                    break;
                }
                return false;
            }

            return true;
        }
    }
}