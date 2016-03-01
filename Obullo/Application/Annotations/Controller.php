<?php

namespace Obullo\Application\Annotations;

use ReflectionClass;
use Interop\Container\ContainerInterface as Container;

/**
 * Annotation Reader
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Controller
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Doc method
     * 
     * @var string
     */
    protected $method = 'index';

    /**
     * ReflectionClass instance
     * 
     * @var object
     */
    protected $reflector;

    /**
     * Set container 
     * 
     * @param Container $container container
     *
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        $this->container->share(
            'annotation.middleware',
            'Obullo\Application\Annotations\Methods'
        )
            ->withArgument($container->get('request'))
            ->withArgument($container->get('middleware'));
    }

    /**
     * Set reflector
     * 
     * @param ReflectionClass $reflector reflection class
     *
     * @return void
     */
    public function setReflectionClass(ReflectionClass $reflector)
    {
        $this->reflector = $reflector;
    }

    /**
     * Set controller method
     * 
     * @param string $method name
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Parse docs blocks and execute filters
     * 
     * @return void
     */
    public function parse()
    {
        $blocks = '';
        if ($this->reflector->hasMethod('__construct')) {
            $blocks = $this->reflector->getMethod('__construct')->getDocComment();
        }
        $blocks.= $this->reflector->getMethod($this->getMethod())->getDocComment();

        $docs = str_replace('*', '', $blocks);
        $docs = explode("@", $docs);

        if (strpos($blocks, 'middleware->') > 0) {

            foreach ($docs as $line) {
                $methods = explode('->', $line);  // explode every methods
                array_shift($methods);            // remove class name "filter"
                foreach ($methods as $methodString) {
                    $this->callMethod($methodString);
                }
            }
        }
    }

    /**
     * Call filter methods
     * 
     * @param string $methodString middleware method name ( when, assign, method )
     * 
     * @return void
     */
    public function callMethod($methodString)
    {
        $strstr = strstr($methodString, '(');
        $params = str_replace(array('(',')',';'), '', $strstr);
        $untrimmed = str_replace($strstr, '', $methodString);
        $method = trim($untrimmed);
        $parray = $params = str_replace(array(' ', '"', "'", '[', ']'), '', trim($params));
        
        if (strpos($params, ',') > 0) {  // array support
            $parray = explode(',', $params);
        }
        $this->container->get('annotation.middleware')->$method($parray);  // Execute middleware methods
    }

    /**
     * Get method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}