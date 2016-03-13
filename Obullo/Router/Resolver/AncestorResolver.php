<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve primary folder
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AncestorResolver
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
     * Argument factor
     * 
     * @var integer
     */
    protected $argumentFactor = 0;

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
        $ancestor = $this->router->getAncestor('/');
        $folder = $this->router->getFolder();
        $hasSegmentOne = empty($segments[1]) ? false : true;
        
        // Add support e.g http://project/widgets/tutorials/helloWorld.php

        if ($hasSegmentOne && is_file(FOLDERS .$ancestor.$folder.'/'.$this->router->ucwordsUnderscore($segments[1]).'.php')) {

            $this->segments = $segments;
            return $this;

        } elseif ($hasSegmentOne && isset($segments[2]) && is_dir(FOLDERS .$ancestor.$folder.'/'.$segments[1])) {

            $this->router->setFolder($folder.'/'.$segments[1]);
            $this->argumentFactor = 1;
            $this->segments = $segments;
            return $this;
        }

        // Add index file support 
        //  Rewrite /widgets/tutorials/tutorials/test to /widgets/tutorials/test

        array_unshift($segments, $folder); 
        $this->segments = $segments;
        return $this;
        
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
        return $this->argumentFactor;
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