<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve directory
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class DirectoryResolver
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

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
        $directory = $this->router->getDirectory();
        $hasSegmentOne = empty($segments[1]) ? false : true;

        $file = MODULES.$directory.'/'.$this->router->ucwordsUnderscore($directory).'.php';

        if (is_file($file)) {

            if ($hasSegmentOne == false || $hasSegmentOne && $segments[1] == 'index') {  // welcome/hello support
                array_unshift($segments, $directory);
            }
            return $segments;
        }
        return $segments;
    }

}