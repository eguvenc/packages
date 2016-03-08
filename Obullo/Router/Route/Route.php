<?php

namespace Obullo\Router\Route;

use Obullo\Router\Domain;
use Obullo\Router\RouterInterface as Router;

/**
 * Route
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Route
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Domain
     * 
     * @var object
     */
    protected $domain;

    /**
     * Domain name
     * 
     * @var string
     */
    protected $domainName;

    /**
     * Route rules
     * 
     * @var array
     */
    protected $routes = array();

    /**
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->domain = $router->getDomain();
        $this->domainName = $this->domain->getName();
    }

    /**
     * Defines http routes
     * 
     * @param string $methods method names
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function add($methods, $match, $rewrite = null, $closure = null)
    {
        $options = ($this->router->getGroup()) ? $this->router->getGroup()->getOptions() : array();

        $this->routes[$this->domainName][] = array(
            'group' => $this->_getGroupNameValue($options),
            'sub.domain' => $this->_getSubDomainValue($this->domainName, $options),
            'when' => $methods, 
            'match' => trim($match, '/'),
            'rewrite' => trim($rewrite, '/'),
            'scheme' => $this->_getSchemeValue($match),
            'closure' => $closure,
        );
    }

    /**
     * Returns true if routes array has no data otherwise false
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->routes);
    }

    /**
     * Gets current domain name routes
     * 
     * @return array
     */
    public function getArray()
    {
        return (empty($this->routes[$this->domainName])) ? false : $this->routes[$this->domainName];
    }

    /**
     * Returns to all routes array
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->routes;
    }

    /**
     * Replace route scheme
     * 
     * @param array $replace scheme data
     * 
     * @return object
     */
    public function addWhere(array $replace)
    {
        $count = count($this->routes) - 1;
        if ($count == -1) {
            return;
        };
        $configDomain = $this->domain->getImmutable();

        if (! empty($this->routes[$this->domainName][$count]['sub.domain'])) {
            $configDomain = $this->routes[$this->domainName][$count]['sub.domain'];
        }
        if ($this->domainName == $configDomain) {

            $replace = $this->addBrackets($replace); // support for 
                                                     // ->where(array('id' => '[0-9]+', 'name' => '[a-z]+', 'any' => '.*'));
                                                     // instead of array('id' => '([0-9]+)'
            $scheme = str_replace(
                array_keys($replace),
                array_values($replace),
                $this->routes[$this->domainName][$count]['scheme']
            );
            $scheme = str_replace(
                array('{','}'),
                array('',''),
                $scheme
            );
            $this->routes[$this->domainName][$count]['match'] = $scheme;
        }
    }

    /**
     * Add brackets to regex
     * 
     * @param array $replace uri replace string
     *
     * @return array
     */
    protected function addBrackets(array $replace)
    {
        $newArray = array();
        foreach ($replace as $key => $value) {
            if (substr($value, 0, 1) != '(') {      // If have not brackets
                $newArray[$key] = '('.$value.')';   // Add brackets to regex for preg_replace() operation
                                                    // in router.php dispatchRouteMatches() func.
            }
        }
        return $newArray;
    }

    /**
     * Get group value
     *
     * @param null|array $options group data
     * 
     * @return string
     */
    private function _getGroupNameValue($options = null)
    {
        if (! isset($options['name'])) {
            $options['name'] = 'UNNAMED';
        }
        return $options['name'];
    }

    /**
     * Get subdomain value
     *
     * @param string     $domain  name
     * @param null|array $options group data
     * 
     * @return mixed
     */
    private function _getSubDomainValue($domain, $options = null)
    {
        if ($this->domain->isSub($domain)) {
            return $options['domain'];
        }
        return null;
    }

    /**
     * Get scheme value
     * 
     * @param string $match param
     * 
     * @return string
     */
    private function _getSchemeValue($match)
    {
        return (strpos($match, '}') !== false) ? trim($match, '/') : null;
    }

}