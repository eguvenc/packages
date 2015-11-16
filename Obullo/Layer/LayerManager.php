<?php

namespace Obullo\Layer;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Layer Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LayerManager implements ServiceInterface
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
     * @param ContainerInterface $c      container
     * @param array              $params service parameters
     */
    public function __construct(Container $c, array $params)
    {
        $this->c = $c;
        $this->c['layer.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['layer'] = function () {

            return new Request(
                $this->c,
                $this->c['logger'],
                $this->c['layer.params']
            );

        };
    }

}