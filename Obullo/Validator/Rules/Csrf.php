<?php

namespace Obullo\Validator\Rules;

use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Validator\FieldInterface as Field;

/**
 * Csrf form verify
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Csrf implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Call next
     * 
     * @param Field    $field object
     * @param Callable $next  object
     * 
     * @return object
     */
    public function __invoke(Field $field, Callable $next)
    {
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

            return $next($field);
        }
    }
}