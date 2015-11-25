<?php

namespace Obullo\Security;

use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\ServiceInterface;

/**
 * Csrf Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CsrfManager implements ServiceInterface
{
    /**
     * Container class
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     * 
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Set service parameters
     * 
     * @param array $params service configuration
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->c['csrf.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['csrf'] = function () {

            return new Csrf(
                $this->c['session'],
                $this->c['logger'],
                $this->c['csrf.params']
            );
        };

    }

}