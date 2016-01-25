<?php

namespace Obullo\Validator\Rules;

use Obullo\Security\CsrfInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;

/**
 * Csrf form verify
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Call next
     * 
     * @param Field $next object
     * 
     * @return object
     */
    public function __invoke(Field $next)
    {
        $field = $next;
        if ($this->isValid($field)) {
            return $next();
        }
        return false;
    }

    /**
     * Check csrf code
     * 
     * @param Field $field field object
     * 
     * @return boolean
     */
    public function isValid(Field $field)
    {
        $container = $this->getContainer();
        $request = $container->get('request');

        if ($request->getMethod() == 'POST') {

            if (! $container->get('csrf')->verify($request)) {

                $code = $container->get('csrf')->getErrorCode();
                
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