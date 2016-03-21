<?php

namespace Obullo\Tests;

use DateTime;
use Traversable;
use Obullo\Cli\Console;
use Obullo\Http\Controller;
use Obullo\Utils\ArrayHelper;
use Obullo\Tests\Constraint\StringContains;
use Obullo\Tests\Constraint\TraversableContains;

/**
 * AbstractController for Http based tests.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class TestController extends Controller implements HttpTestInterface
{
    protected $_data = array();     // View data
    protected $_varDump = null;     // Var dump variable
    protected $_errors = array();   // Login trait errors
    protected $_commands = array(); // Method commands
    protected $_disabledMethods = array();  // Disabled method array
    protected $_disableConsole = false;

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
        if ($contentType == 'console') {

            $queryParams = $this->request->getQueryParams();
            /**
             * Suite mode
             */
            if (! empty($queryParams['suite'])) {
                $results = [
                    'disabled' => $this->checkConsoleAccess(),
                    'class' => rtrim($this->request->getUri()->getPath(), "/"),
                    'methods' => $this->getClassMethods(),
                ];
                return $this->response->json($results);
            }
            /**
             * Single mode
             */
            echo Console::logo("Welcome to Test Manager (c) 2016");
            echo Console::newline(2);

            $Class = $this->url->anchor("tests/", ucfirst($this->router->getClass()));

            echo Console::text(strip_tags($Class). " Class", "yellow");
            echo Console::newline(2);

            $content = str_replace(array("<br >", "<br>", "<br />"), "\n", $this->getHtmlClassMethods());
            $methods = explode("\n", strip_tags(trim($content, "\n")));

            foreach ($methods as $value) {
                echo Console::text("- ".$value, "yellow");
                echo Console::newline(1);
            }
            echo Console::newline(1);
            return;
        }
    }

    /**
     * Disabled console
     * 
     * @return void
     */
    public function disableConsole()
    {
        $this->_disableConsole = true;
    }

    /**
     * Check console access
     * 
     * @return bool
     */
    public function checkConsoleAccess()
    {
        return $this->_disableConsole;
    }

    /**
     * Disabled methods
     * 
     * @param array $methods methods
     *
     * @return void
     */
    public function setDisabledMethods(array $methods)
    {
        $this->_disabledMethods = $methods;
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
            '__generateConsoleResponse',
            '__getParsedCommandBody',
            '__convertToDateTime',
            'getClassMethods',
            'getHtmlClassMethods',
            'generateTestResults',
            'varDump',
            'setError',
            'setDisabledMethods',
            'setCommandRefresh',
            'newLoginRequest',
            'assertTrue',
            'assertFalse',
            'assertNull',
            'assertEqual',
            'assertEmpty',
            'assertNotEmpty',
            'assertNotEqual',
            'assertInstanceOf',
            'assertArrayHasKey',
            'assertArrayNotHasKey',
            'assertContains',
            'assertGreaterThan',
            'assertLessThan',
            'assertInternalType',
            'assertNotInternalType',
            'assertNotType',
            'assertObjectHasAttribute',
            'assertObjectNotHasAttribute',
            'assertThat',
            'assertArrayContains',
            'assertStringContains',
            'assertDate',
        ];
        $disabledMethods = array_merge($disabledMethods, $this->_disabledMethods);
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
     * @return string|array
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
        $pass = false;
        if ($x === true) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($x === false) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }

    /**
     * Assert null
     * 
     * @param mixed $x       value
     * @param mixed $message message
     * 
     * @return boolean
     */
    public function assertNull($x, $message = "")
    {
        $pass = false;
        if ($x === null) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($x == $y) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($x != $y) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($y instanceof $x) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
    public function assertArrayHasKey($needle, $haystack, $message = "")
    {
        $pass = false;
        if (! empty($haystack)
            && is_string($needle)
            && is_array($haystack)
            && array_key_exists($needle, $haystack)
        ) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }

    /**
     * Opposite of assert array has a key
     * 
     * @param mixed $needle   value
     * @param mixed $haystack value
     * @param mixed $message  message
     * 
     * @return boolean
     */
    public function assertArrayNotHasKey($needle, $haystack, $message = "")
    {
        $pass = false;
        if (! empty($haystack)
            && is_string($needle)
            && is_array($haystack)
            && ! array_key_exists($needle, $haystack)
        ) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }

    /**
     * Assert contains
     * 
     * @param array  $needle   needle
     * @param array  $haystack haystack
     * @param string $message  message 
     * 
     * @return boolean
     */
    public function assertArrayContains($needle, $haystack, $message = "")
    {
        $pass = false;
        if (is_string($needle) || is_object($needle)) {
            if (in_array($needle, $haystack, true)) {
                $this->__add(['pass' => true, 'message' => $message]);
                $pass = true;
            }
        }
        if (is_object($haystack) && $haystack instanceof Traversable) {
            $haystack = ArrayHelper::iteratorToArray($haystack);
        }
        if (is_array($needle)) {
            if (ArrayHelper::contains($needle, $haystack)) {
                $pass = true;
            }
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }

    /**
     * Assert string contains
     * 
     * @param string $needle     needle
     * @param array  $haystack   haystack
     * @param string $message    message 
     * @param boolea $ignoreCase ignore case sensitive for string contains
     * 
     * @return boolean
     */
    public function assertStringContains($needle, $haystack, $message = "", $ignoreCase = false)
    {
        if (!is_string($needle)) {
            throw InvalidArgumentHelper::factory(
                1,
                'string'
            );
        }
        $constraint = new StringContains(
            $needle,
            $ignoreCase
        );
        return $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * Evaluates a constraint matcher object.
     *
     * @param mixed      $value      value
     * @param constraint $constraint constraint
     * @param string     $message    message
     *
     * @return void
     */
    public function assertThat($value, $constraint, $message = '')
    {
        $pass = false;
        if ($constraint->matches($value)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($x > $y) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if ($x < $y) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if (empty($x)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        $pass = false;
        if (! empty($x)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        // case ($expected == 'alphaunicode'):
        //     $pass = "";
        //     break;
        case ($expected == 'alnum'):
            $pass = ctype_alnum($actual);
            break;
        // case ($expected == 'alnumunicode'):
        //     $pass = '';
        //     break;
        // case ($expected == 'digitunicode'):
        //     $pass = '';
        //     break;
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
    public function assertInternalType($expected, $actual, $message = "")
    {
        $pass = false;
        if ($this->__checkType($expected, $actual)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
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
    public function assertNotInternalType($expected, $actual, $message = "")
    {
        $pass = false;
        if (false == $this->__checkType($expected, $actual)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }
    /**
     * Assert date
     * 
     * @param mixe   $date    value
     * @param string $message message
     * 
     * @return bool
     */
    public function assertDate($date, $message = "")
    {
        $pass = false;
        if ($this->__convertToDateTime($date)) {
            $pass = true;
        }
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }    

     /**
     * Attempts to convert an int, string, or array to a DateTime object
     *
     * @param string|int|array $value value
     * 
     * @return bool
     */
    protected function __convertToDateTime($value)
    {
        if ($value instanceof DateTime) {
            return true;
        }
        if (is_integer($value)) {
            $value = @date("Y-m-d H:i:s", $value);
        }
        return date_create("$value");
    }

    /**
     * Assert has attribute
     * 
     * @param string $attribue attribute
     * @param mixed  $object   class name or object
     * @param string $message  message
     * 
     * @return boolean
     */
    public function assertObjectHasAttribute($attribue, $object, $message = "")
    {
        $pass = property_exists($object, $attribue); 
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
    }
    
    /**
     * Assert has not attribute
     * 
     * @param string $attribue attribute
     * @param mixed  $object   class name or object
     * @param string $message  message
     * 
     * @return boolean
     */
    public function assertObjectNotHasAttribute($attribue, $object, $message = "")
    {
        $pass = property_exists($object, $attribue) ? false : true;
        $this->__add(['pass' => $pass, 'message' => $message]);
        return $pass;
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

        switch ($contentType) {
        case 'json':
            if (! empty($this->_errors)) {
                return $this->response->json(array('errors' => $this->_errors));
            }
            return $this->__generateJsonResponse();
            break;
        case 'console':
            if (! empty($this->_errors)) {
                // var_dump($this->errors);
            }
            return $this->__generateConsoleResponse();
            break;
        default:
            if (! empty($this->_errors)) {
                foreach ($this->_errors as $error) {
                    $this->view->load(
                        'templates::error',
                        [
                            'header' => $error['header'].' Error',
                            'error' => $error['message']
                        ]
                    );
                }
                return;
            }
            return $this->__generateHtmlResponse();
            break;
        }
    }

    /**
     * Returns to content type (console, json, html)
     * 
     * @return string
     */
    protected function __getContentType()
    {
        $query  = $this->request->getQueryParams();
        $server = $this->request->getServerParams();

        if (defined('STDIN') && ! empty($server['argv'][0]) && strpos($server['argv'][0], "test") === 0) {
            return 'console';
        }
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
     * Generates console content
     * 
     * @return void
     */
    protected function __generateConsoleResponse()
    {
        $queryParams = $this->request->getQueryParams();
        $ancestor = $this->router->getAncestor();
        $folder = $this->router->getFolder();
        $class  = $this->router->getClass();
        $method = $this->router->getMethod();

        $passes = 0;
        $failures = 0;
        $results = array();
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
        $assertions = count($this->_data);
        $completed  = $passes + $failures;

        if (! empty($queryParams['suite'])) {
            return $this->response->json(
                [
                    'assertions' => $assertions,
                    'passes' => $passes,
                    'failures' => $failures,
                ]
            );
        }
        echo Console::logo("Welcome to Test Manager (c) 2016");
        echo Console::newline(2);
        echo Console::text(
            'Results of "'. $ancestor.'/'.$folder.'/'.lcfirst($class).'->'.$method.'()"',
            "yellow"
        );
        echo Console::newline(2);

        foreach ($this->_data as $data) {
            if ($data['pass']) {
                echo Console::text("* ".$data['message'], "yellow");
                echo Console::newline(1);
                echo Console::text("Pass", "green");
                echo Console::newline(1);
            } else {
                echo Console::text("*".$data['message'], "yellow");
                echo Console::newline(1);
                echo Console::text("Fail", "red");
                echo Console::newline(1);
            }
        }
        echo Console::newline(1);
        echo Console::text("$assertions/$completed tests complete: ", "yellow", true);
        echo Console::text("$passes passes and $failures fails.", "yellow");
        echo Console::newline(2);
    }

    /**
     * Generates json response
     * 
     * @return void
     */
    protected function __generateJsonResponse()
    {
        $passes = 0;
        $failures = 0;
        $results = array();
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
                'commands' => $this->_commands,
                'total' => [
                    'passes' => $passes,
                    'fails' => $failures,
                    'assertions' => count($this->_data),
                ]
            )
        );
    }

}