<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve module
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ModuleResolver
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
        $module = $this->router->getModule('/');
        $directory = $this->router->getDirectory();
        $hasSegmentOne = empty($segments[1]) ? false : true;
        
        // Add support e.g http://project/widgets/tutorials/helloWorld.php

        if ($hasSegmentOne && is_file(MODULES .$module.$directory.'/'.$this->router->ucwordsUnderscore($segments[1]).'.php')) {

            $this->segments = $segments;

            return $this;

        } else {
            
            // Add index file support 
            //  Rewrite /widgets/tutorials/tutorials/test to /widgets/tutorials/test

            array_unshift($segments, $directory); 
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