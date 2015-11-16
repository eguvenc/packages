<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve module
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
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

        if ($hasSegmentOne && is_file(MODULES.$module.$directory.'/'.$this->router->ucwordsUnderscore($segments[1]).'.php')) {

            return $segments;

        } else {
            
            // Add index file support 
            //  Rewrite /widgets/tutorials/tutorials/test to /widgets/tutorials/test

            array_unshift($segments, $directory); 
            return $segments;
        }

        return $segments;
    }

}