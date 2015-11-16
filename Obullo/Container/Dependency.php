<?php

namespace Obullo\Container;

use ReflectionClass;
use RuntimeException;

/**
 * Dependency Manager
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Dependency
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Components
     * 
     * @var array
     */
    protected $components;

    /**
     * Dependecies
     * 
     * @var array
     */
    protected $dependencies;

    /**
     * Set container
     * 
     * @param ContainerInterface|null $c Container
     *
     * @return object
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Add component 
     * 
     * @param integer $key   container id
     * @param string  $class class path
     *
     * @return void
     */
    public function addComponent($key, $class)
    {
        if ($this->c->has($key)) {
            return;
        }
        $this->components[$key] = $class;
        $this->c[$key] = function () use ($class) {
            return $this->resolveDependencies($class);
        };
    }

    /**
     * Add class to dependecies
     * 
     * @param string $cid class name
     * 
     * @return object
     */
    public function addDependency($cid)
    {
        $this->dependencies[$cid] = $cid;
        return $this;
    }

    /**
     * Remove class from dependencies
     * 
     * @param string $cid 
     * 
     * @return void
     */
    public function removeDependency($cid)
    {
        if (isset($this->dependencies[$cid])) {
            unset($this->dependencies[$cid]);
        }
    }

    /**
     * Resolve dependecies
     * 
     * @param string $class path
     * 
     * @return object class instance
     */
    public function resolveDependencies($class)
    {
        $Class = '\\'.ltrim($class, '\\');
        $reflector = new ReflectionClass($Class);
        if (! $reflector->hasMethod('__construct')) {
            return $reflector->newInstance();
        } else {
            return $reflector->newInstanceArgs($this->resolveParams($reflector));
        }
    }

    /**
     * Resolve dependecy parameters
     * 
     * @param \ReflectionClass $reflector reflection instance
     *
     * @return array params
     */
    protected function resolveParams(ReflectionClass $reflector)
    {
        $parameters = $reflector->getConstructor()->getParameters();
        $params = array();

        $deps = $this->getDependencies();
        $services = $this->getServices();
        $components = $this->getComponents();

        foreach ($parameters as $parameter) {
            $d = $parameter->getName();

            if ($d == 'c' || $d == 'container') {
                $params[] = $this->c;
            } else {
                $isDependency = isset($deps[$d]);
                if ($isDependency) {
                    $params[] = $this->c[$d];
                } else {

                    $isService = isset($services[$d]);
                    $isComponent = isset($components[$d]);

                    if ($isService || $isComponent) {  // Detect missing dependecy
                        throw new RuntimeException(
                            sprintf(
                                'Dependency "%s" is missing for "%s" component.',
                                $parameter->getClass()->name,
                                $d
                            )
                        );
                    }
                }
            }
        }
        return $params;
    }

    /**
     * Get all services
     * 
     * @return array
     */
    public function getServices()
    {
        return $this->c->getServices();
    }

    /**
     * Get all dependencies
     * 
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Get all components
     * 
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

}
