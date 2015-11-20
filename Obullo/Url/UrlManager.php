<?php

namespace Obullo\Url;

use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\ServiceInterface;

/**
 * Url Service Manager
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UrlManager implements ServiceInterface
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
     * @param ContainerInterface $c container
     */
    public function __construct(Container $c)
    {
        $this->c = $c;
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
        $params['webhost'] = $this->c['config']['http']['webhost'];
        
        $this->c['url.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object url
     */
    public function register()
    {
        $this->c['url'] = function () {
            
            return new Url(
                $this->c['request'],
                $this->c['logger'],
                $this->c['url.params']
            );
        };
    }

}