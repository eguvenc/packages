<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ClassResolver
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    protected $segments;

    /**
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Resolve
     * 
     * @param array $segments uri segments
     * 
     * @return array resolved segments
     */
    public function resolve(array $segments)
    {
        $this->segments = $segments;

        return $this;
        // return $segments;
    }

    public function getFactor()
    {
        return -1;
    }

    public function getSegments()
    {
        return $this->segments;
    }    

}