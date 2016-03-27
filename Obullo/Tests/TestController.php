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
    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        $contentType = TestHelper::getContentType($this->request);

        if ($contentType == 'html') {
            $this->view->load(
                'tests::index',
                ['content' => TestHelper::getHtmlClassMethods($this, $this->container)]
            );
        }
        if ($contentType == 'console') {

            $queryParams = $this->request->getQueryParams();
            /**
             * Suite mode
             */
            if (! empty($queryParams['suite'])) {
                $results = [
                    'disabled' => TestPreferences::isIgnored('console'),
                    'class' => rtrim($this->request->getUri()->getPath(), "/"),
                    'methods' => TestHelper::getClassMethods($this),
                ];
                return $this->response->json($results);
            }
            TestOutput::generateConsoleFileView($this, $this->container);
            return;
        }
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
                TestOutput::setData(['pass' => true, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        if (TestHelper::checkType($expected, $actual)) {
            $pass = true;
        }
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        if (false == TestHelper::checkType($expected, $actual)) {
            $pass = true;
        }
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        if (TestHelper::convertToDateTime($date)) {
            $pass = true;
        }
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
        return $pass;
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
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
        TestOutput::setData(['pass' => $pass, 'message' => $message]);
        return $pass;
    }

    /**
     * Generate test results
     * 
     * @return void
     */
    public function __generateTestResults()
    {
        $contentType = TestHelper::getContentType($this->request);

        switch ($contentType) {
        case 'console':
            if (TestOutput::hasError()) {
                foreach (TestOutput::getErrors() as $error) {
                    echo Console::fail($error);
                    echo Console::newline(1);
                }
            }
            return $this->__generateConsoleResponse();
            break;
        default:
            if (TestOutput::hasError()) {
                foreach (TestOutput::getErrors() as $error) {
                    $this->view->load(
                        'templates::error',
                        [
                            'header' => 'Test Error',
                            'error' => $error
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
     * Generates html content
     * 
     * @return void
     */
    protected function __generateHtmlResponse()
    {
        $results = '';
        foreach (TestOutput::getData() as $data) {
            if ($data['pass']) {
                $results.= $this->view->get('tests::pass', ['message' => $data['message']]);
            } else {
                $results.= $this->view->get('tests::fail', ['message' => $data['message']]);
            }
        }
        $this->view->load('tests::result', ['dump' => TestOutput::getVarDumpArray(), 'results' => $results]);
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
        foreach (TestOutput::getData() as $data) {

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
        $assertions = count(TestOutput::getData());
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
        $class = $ancestor.'/'.$folder.'/'.lcfirst($class).'->'.$method.'()';
        $stats = [
            'c' => $completed,
            'a' => $assertions,
            'p' => $passes,
            'f' => $failures
        ];
        TestOutput::generateConsoleView($data, $class, $stats);
    }

}