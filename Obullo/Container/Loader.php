<?php

namespace Obullo\Container;

use RuntimeException;
use Obullo\Container\ServiceInterface;

/**
 * Php Service Loader for Obullo Container
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Loader implements ContainerAwareInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Service loader extension
     * 
     * @var string
     */
    protected $ext;

    /**
     * Register service path
     * 
     * @var string
     */
    protected $path;

    /**
     * Service definitions
     * 
     * @var array
     */
    protected $services;

    /**
     * Service lazy loading
     * 
     * @var array
     */
    protected $registered;

    /**
     * Register config loader path
     * 
     * @param string $path   path
     * @param string $loader name
     */
    public function __construct($path, $loader = 'php')
    {
        $this->ext = strtolower($loader);
        $this->path = $path;
    }

    /**
     * Set Container
     * 
     * @param ContainerInterface|null $c container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $c = null)
    {
        $this->c = $c;
        $this->services = $c->getServices();
    }

    /**
     * Resolve service
     * 
     * @param string $class container id
     * 
     * @return boolean
     */
    public function resolveServices($class)
    {
        $cid = strtolower($class);  // Service container id

        if (isset($this->services[$cid])) {  // Resolve services

            $Class = '\\'.ltrim($this->services[$cid], '\\');
            if (! isset($this->registered[$cid])) {  // Service lazy loader

                $loaderClass = '\\Obullo\Container\Loader\\'.ucfirst($this->ext).'ServiceLoader';
                $loader = new $loaderClass;
                $config = $loader->load($this->getFile($cid));

                if (! isset($config['params'])) {
                    $config['params'] = array();
                }
                $service = new $Class($this->c, $config['params']);
                if ($service instanceof ServiceInterface) {
                    $service->register();
                }
                if (! $this->c->has($cid)) {
                    throw new RuntimeException(
                        sprintf(
                            "%s service configuration error service class name must be same with container key.",
                            $cid
                        )
                    );
                }
                $loader->callMethods($this->c[$cid], $config);  // Run service methods
                $this->registered[$cid] = true;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns to service path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file full path
     * 
     * @param string $name filename
     * 
     * @return string
     */
    public function getFile($name)
    {
        return rtrim($this->getPath(), '/') .'/'.$name.'.'.$this->ext;
    }

}