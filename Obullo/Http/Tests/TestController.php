<?php

namespace Obullo\Http\Tests;

use Obullo\Http\Controller;

/**
 * AbstractController for Http based tests.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class TestController extends Controller implements HttpTestInterface
{
    protected $_data = array();    // View data
    protected $_varDump = null;    // Var dump variable
    protected $_errors = array();  // Login trait errors

    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        $contentType = $this->__getContentType();

        if ($contentType == 'html') {
            $this->view->load(
                'tests::index',
                ['content' => $this->getHtmlClassMethods()]
            );
        }
        if ($contentType == 'json') {
            return $this->response->json(
                array(
                    'class' => [
                        'name' => $this->router->getNamespace().$this->router->getClass(),
                        'methods' => $this->getClassMethods()
                    ]
                )
            );
        }
    }

    /**
     * Returns to class methods
     * 
     * @return array
     */
    public function getClassMethods()
    {
        $disabledMethods = [
            'index',
            '__construct',
            '__get',
            '__set',
            '__add',
            '__checkType',
            '__getContentType',
            '__generateHtmlResponse',
            '__generateJsonResponse',
            'getClassMethods',
            'getHtmlClassMethods',
            'generateTestResults',
            'varDump',
            'setError',
            'newLoginRequest',
            'assertTrue',
            'assertFalse',
            'assertEqual',
            'assertEmpty',
            'assertNotEmpty',
            'assertNotEqual',
            'assertInstanceOf',
            'assertHas',
            'assertContains',
            'assertGreaterThan',
            'assertLessThan',
            'assertType',
            'assertNotType',
        ];
        $methods = array();
        foreach (get_class_methods($this) as $name) {
            if (! in_array($name, $disabledMethods))
            $methods[] = $name;
        }
        return $methods;
    }

    /**
     * Get class methods as html
     * 
     * @return string
     */
    public function getHtmlClassMethods()
    {
        $html = "";
        foreach ($this->getClassMethods() as $name) {
            $html.= $this->url->anchor(rtrim($this->request->getUri()->getPath(), "/")."/".$name, $name)."<br>";
        }
        return $html;
    }

    /**
     * Set http test errors then we send them as json response
     * 
     * @param string $error  error
     * @param string $header error header
     *
     * @return void
     */
    public function setError($error, $header = "Test")
    {
        $this->_errors[] = array('message' => $error, 'header' => $header);
    }

    /**
     * Assert true
     * 
     * @param mixed $x       value
     * @param mixed $message message
     * 
     * @return boolean
     */
    public function assertTrue($x, $message = "")
    {
        if ($x === true) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert false
     * 
     * @param mixed $x       value
     * @param mixed $message message
     * 
     * @return boolean
     */
    public function assertFalse($x, $message = "")
    {
        if ($x === false) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert equal
     * 
     * @param mixed $x       value
     * @param mixed $y       value
     * @param mixed $message message
     * 
     * @return boolean
     */
    public function assertEqual($x, $y, $message = "")
    {
        if ($x == $y) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert Not equal
     * 
     * @param mixed $x       value
     * @param mixed $y       value
     * @param mixed $message message
     * 
     * @return boolean
     */
    public function assertNotEqual($x, $y, $message = "")
    {
        if ($x != $y) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert instance of
     * 
     * @param string $x       class name
     * @param object $y       object
     * @param string $message message 
     * 
     * @return boolean
     */
    public function assertInstanceOf($x, $y, $message = "")
    {
        if ($y instanceof $x) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert array has a key
     * 
     * @param mixed $needle   value
     * @param mixed $haystack value
     * @param mixed $message  message
     * 
     * @return boolean
     */
    public function assertHas($needle, $haystack, $message = "")
    {
        if (array_key_exists($needle, $haystack)) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert contains
     * 
     * @param string|array $needle   needle
     * @param array        $haystack haystack
     * @param string       $message  message 
     * 
     * @return boolean
     */
    public function assertContains($needle, array $haystack, $message = "")
    {
        if (is_string($needle) || is_object($needle)) {
            if (in_array($needle, $haystack, true)) {
                $this->__add(['pass' => true, 'message' => $message]);
                return true;
            }
        }
        if (is_array($needle) && count(array_intersect($needle, $haystack)) == count($needle)) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert greater than
     * 
     * @param string $x       class name
     * @param object $y       object
     * @param string $message message 
     * 
     * @return boolean
     */
    public function assertGreaterThan($x, $y, $message = "")
    {
        if ($x > $y) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    } 

    /**
     * Assert less than
     * 
     * @param string $x       class name
     * @param object $y       object
     * @param string $message message 
     * 
     * @return boolean
     */
    public function assertLessThan($x, $y, $message = "")
    {
        if ($x < $y) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    }

    /**
     * Assert empty
     * 
     * @param mixed  $x       data
     * @param string $message message 
     * 
     * @return boolean
     */
    public function assertEmpty($x, $message = "")
    {
        if (empty($x)) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    } 

    /**
     * Assert Not empty
     * 
     * @param mixed  $x       data
     * @param string $message message 
     * 
     * @return boolean
     */
    public function assertNotEmpty($x, $message = "")
    {
        if (! empty($x)) {
            $this->__add(['pass' => true, 'message' => $message]);
            return true;
        }
        $this->__add(['pass' => false, 'message' => $message]);
        return false;
    } 

    /**
     * Get internal type
     * 
     * @param string $expected value
     * @param mixed  $actual   value
     * 
     * @return boolean
     */
    protected function __checkType($expected, $actual)
    {
        switch ($expected) {
        case ($expected == 'integer' || $expected == 'int'):
            $pass = is_int($actual);
            break;
        case ($expected == 'boolean' || $expected == 'bool'):
            $pass = is_bool($actual);
            break;
        case ($expected == 'string'):
            $pass = is_string($actual);
            break;
        case ($expected == 'object'):
            $pass = is_object($actual);
            break;    
        case ($expected == 'float'):
            $pass = is_float($actual);
            break;
        case ($expected == 'double'):
            $pass = is_double($actual);
            break;
        case ($expected == 'resource'):
            $pass = is_resource($actual);
            break;
        case ($expected == 'null'):
            $pass = is_null($actual);
            break;
        case ($expected == 'numeric'):
            $pass = is_numeric($actual);
            break;
        case ($expected == 'scalar'):
            $pass = is_scalar($actual);
            break;
        case ($expected == 'alpha'):
            $pass = ctype_alpha($actual);
            break;
        case ($expected == 'alnum'):
            $pass = ctype_alnum($actual);
            break;
        case ($expected == 'digit'):
            $pass = ctype_digit($actual);
            break;
        case ($expected == 'lower'):
            $pass = ctype_lower($actual);
            break;
        }
        return $pass;
    }

    /**
     * Assert internal type
     * 
     * @param string $expected value
     * @param mixed  $actual   value
     * @param mixed  $message  value
     * 
     * @return boolean
     */
    public function assertType($expected, $actual, $message = "")
    {
        $pass = false;
        if ($this->__checkType($expected, $actual)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
    }

    /**
     * Assert internal type
     * 
     * @param string $expected value
     * @param mixed  $actual   value
     * @param mixed  $message  value
     * 
     * @return boolean
     */
    public function assertNotType($expected, $actual, $message = "")
    {
        $pass = false;
        if (false == $this->__checkType($expected, $actual)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
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
    protected function __add($data = array())
    {
        $this->_data[] = $data;
    }

    /**
     * Generate test results
     * 
     * @return void
     */
    public function generateTestResults()
    {
        $contentType = $this->__getContentType();

        if (! empty($this->_errors)) {
            if ($contentType == 'json') {
                return $this->response->json(array('errors' => $this->_errors));
            } else {
                foreach ($this->_errors as $error) {
                    $this->view->load('templates::error', ['header' => $error['header'].' Error', 'error' => $error['message']]);
                }
                return;
            }
        }
        if ($contentType == 'json') {
            return $this->__generateJsonResponse();
        }
        $this->__generateHtmlResponse();
    }

    /**
     * Returns to content type (json, html)
     * 
     * @return string
     */
    protected function __getContentType()
    {
        $query = $this->request->getQueryParams();
        return isset($query['response']) ? $query['response'] : 'html';
    }

    /**
     * Generates html content
     * 
     * @return void
     */
    protected function __generateHtmlResponse()
    {
        $results = '';
        foreach ($this->_data as $data) {
            if ($data['pass']) {
                $results.= $this->view->get('tests::pass', ['message' => $data['message']]);
            } else {
                $results.= $this->view->get('tests::fail', ['message' => $data['message']]);
            }
        }
        $this->view->load('tests::result', ['dump' => $this->_varDump, 'results' => $results]);
    }
    /**
     * Generates json response
     * 
     * @return void
     */
    protected function __generateJsonResponse()
    {
        $results = array();
        $passes = 0;
        $failures = 0;
        foreach ($this->_data as $data) {
            if ($data['pass']) {
                ++$passes;
                $results[] = array(
                    'message' => $data['message'],
                    'pass' => true,
                );
            } else {
                ++$failures;
                $results[] = array(
                    'message' => $data['message'],
                    'pass' => false,
                );
            }
        }
        return $this->response->json(
            array(
                'results' => $results,
                'dump'  => $this->_varDump,
                'total' => ['passes' => $passes,'fails' => $failures]
            )
        );
    }

}