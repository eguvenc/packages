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
     * Segments
     * 
     * @var array
     */
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
        $directory = $this->router->getDirectory();
        $hasSegmentOne = empty($segments[1]) ? false : true;

        $file = MODULES .$directory.'/'.$this->router->ucwordsUnderscore($directory).'.php';

        if (is_file($file)) {

            $index = ($hasSegmentOne && $segments[1] == 'index');

            if ($hasSegmentOne == false || $index) {  // welcome/hello support
                array_unshift($segments, $directory);
            }
            $this->segments = $segments;

            return $this;
        }

        $this->segments = $segments;

        return $this;
    }

    /**
     * Get segment factor
     * 
     * @return int
     */
    public function getFactor()
    {
        return 0;
    }

    /**
     * Get uri segments
     * 
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

}