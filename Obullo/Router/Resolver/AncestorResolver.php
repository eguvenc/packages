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
    protected $arity = 0;

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
        $folders  = $this->getSubFolders($ancestor, $segments);

        $this->router->setFolderArray($folders);
        $this->router->setFolder(implode("/", $folders));

        $folder = $this->router->getFolder();
        $arity  = count($folders) -1;
        $hasSegmentOne = empty($segments[1]) ? false : true;
        
        // Add support e.g http://project/widgets/tutorials/helloWorld.php

        if ($hasSegmentOne && is_file(FOLDERS .$ancestor.$folder.'/'.$this->router->ucwordsUnderscore($segments[1]).'.php')) {

            $this->segments = $segments;
            return $this;
        }
        if ($hasSegmentOne && isset($segments[2]) && is_dir(FOLDERS .$ancestor.$folder)) {

            $this->arity = $arity;
            $this->segments = $segments;
            return $this;
        }

        // Add index file support 
        //  Rewrite /widgets/tutorials/tutorials/test to /widgets/tutorials/test

        array_unshift($segments, $folder); 
        $this->segments = $segments;
        return $this;
    }


    /**
     * Returns to sub folders if they exist
     * 
     * @param string $ancestor ancestor folder
     * @param array  $segments uri segments
     * 
     * @return array
     */
    protected function getSubFolders($ancestor, $segments)
    {
        $append  = "";
        $temp = [];
        foreach ($segments as $key => $folder) {

            if ($key > 3) {  // Subfolder level limit
                continue;
            }
            if (isset($temp[$key - 1])) {
                $append = $temp[$key - 1];
            }
            if (is_dir(FOLDERS .$ancestor.$append."/".$folder)) {
                $temp[$key]    = $append."/".$folder;
                $folders[$key] = $folder;
            }
        }
        return $folders;
    }

    /**
     * Get arity
     * 
     * @return int
     */
    public function getArity()
    {
        return $this->arity;
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