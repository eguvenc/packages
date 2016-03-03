<?php

namespace Obullo\Http;

/**
 * Obullo Test Based Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestController
{
    /**
     * Container
     * 
     * @var object
     */
    public $container;

    /**
     * View data
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * Dump variable
     * 
     * @var mixed
     */
    protected $_varDump = null;

    /**
     * Controller instance
     * 
     * @var object
     */
    public static $instance = null;
    
    /**
     * Returns to class methods
     * 
     * @return string
     */
    public function getClassMethods()
    {
        $disabledMethods = [
            'index',
            '__construct',
            '__get',
            '__set',
            'getViewName',
            'getClassMethods',
            '_add',
            '_generateTestResults',
            'varDump',
            'assertTrue',
            'assertFalse',
            'assertEqual',
            'assertInstanceOf',
            'assertContains',
        ];
        $html = "";
        $methods = get_class_methods($this);
        foreach ($methods as $name) {
            if (! in_array($name, $disabledMethods))
            $html.= $this->url->anchor(rtrim($this->request->getRequestTarget(), "/")."/".$name, $name)."<br>";
        }
        return $html;
    }

    /**
     * Creates view filename
     * 
     * @return string
     */
    public function getViewName()
    {
        $class = explode("\\", get_class($this));
        return strtolower(end($class));
    }

    /**
     * Assert true
     * 
     * @param mixed $x    value
     * @param mixed $desc description
     * 
     * @return boolean
     */
    public function assertTrue($x, $desc = "")
    {
        if ($x === true) {
            $this->_add(['pass' => true, 'desc' => $desc]);
            return true;
        }
        $this->_add(['pass' => false, 'desc' => $desc]);
        return false;
    }

    /**
     * Assert false
     * 
     * @param mixed $x    value
     * @param mixed $desc description
     * 
     * @return boolean
     */
    public function assertFalse($x, $desc = "")
    {
        if ($x === false) {
            $this->_add(['pass' => true, 'desc' => $desc]);
            return true;
        }
        $this->_add(['pass' => false, 'desc' => $desc]);
        return false;
    }

    /**
     * Assert equal
     * 
     * @param mixed $x    value
     * @param mixed $y    value
     * @param mixed $desc description
     * 
     * @return boolean
     */
    public function assertEqual($x, $y, $desc = "")
    {
        if ($x === $y) {
            $this->_add(['pass' => true, 'desc' => $desc]);
            return true;
        }
        $this->_add(['pass' => false, 'desc' => $desc]);
        return false;
    }

    /**
     * Assert instance of
     * 
     * @param string $x    class name
     * @param object $y    object
     * @param string $desc description 
     * 
     * @return boolean
     */
    public function assertInstanceOf($x, $y, $desc = "")
    {
        if ($y instanceof $x) {
            $this->_add(['pass' => true, 'desc' => $desc]);
            return true;
        }
        $this->_add(['pass' => false, 'desc' => $desc]);
        return false;
    }

    /**
     * Assert contains
     * 
     * @param string|array $needle   needle
     * @param array        $haystack haystack
     * @param string       $desc     description 
     * 
     * @return boolean
     */
    public function assertContains($needle, array $haystack, $desc = "")
    {
        if (is_string($needle) || is_object($needle)) {
            if (in_array($needle, $haystack, true)) {
                $this->_add(['pass' => true, 'desc' => $desc]);
                return true;
            }
        }
        // http://stackoverflow.com/questions/9655687/php-check-if-array-contains-all-array-values-from-another-array

        if (is_array($needle) && count(array_intersect($needle, $haystack)) == count($needle)) {
            $this->_add(['pass' => true, 'desc' => $desc]);
            return true;
        }
        $this->_add(['pass' => false, 'desc' => $desc]);
        return false;
    }

    /**
     * Dump output
     *
     * @param mixed $value dump value
     * 
     * @return string
     */
    public function varDump($value)
    {
        $this->_varDump = $value;
    }

    /**
     * Add view data
     * 
     * @param array $data data
     * 
     * @return void
     */
    protected function _add($data = array())
    {
        $this->_data[] = $data;
    }

    /**
     * Generate test results
     * 
     * @return void
     */
    public function _generateTestResults()
    {
        if ($this->router->getMethod() == 'index') {  // Disable for index method
            return;
        }
        $results = '';
        foreach ($this->_data as $data) {
            if ($data['pass']) {
                $results.= $this->view->get('tests::pass', ['desc' => $data['desc']]);
            } else {
                $results.= $this->view->get('tests::fail', ['desc' => $data['desc']]);
            }
        }
        $this->view->load('tests::result', ['dump' => $this->_varDump, 'results' => $results]);
    }

    /**
     * Container proxy
     * 
     * @param string $key key
     * 
     * @return object Controller
     */
    public function __get($key)
    {
        if (self::$instance == null || in_array($key, ['request', 'router', 'view'])) {  // Create new layer for each core classes ( Otherwise Layer does not work )
            self::$instance = &$this;
        }
        return $this->container->get($key);
    }

    /**
     * We prevent to set none object variables
     *
     * Forexample in controller this is not allowed $this->user_variable = 'hello'.
     * 
     * @param string $key string
     * @param string $val mixed
     *
     * @return void 
     */
    public function __set($key, $val)  // Custom variables is not allowed !!! 
    {
        if (is_object($val)) {
            $this->{$key} = $val; // WARNING : Store only object types otherwise container params
                                  // variables come in here.
        }
    }
}