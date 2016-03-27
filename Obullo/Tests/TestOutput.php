<?php

namespace Obullo\Tests;

use Obullo\Cli\Console;

/**
 * Test output helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestOutput
{
    /**
     * Add output data
     * 
     * @var array
     */
    protected static $data = array();
    
    /**
     * Errors
     * 
     * @var array
     */
    protected static $errors = array();

    /**
     * Dump data
     * 
     * @var array
     */
    protected static $dumpArray = array();

    /**
     * Dump output
     * 
     * @param string $output output
     * 
     * @return voi
     */
    public static function varDump($output)
    {
        self::$dumpArray[] = $output;
    }

    /**
     * Returns to dump array
     * 
     * @return array
     */
    public static function getVarDumpArray()
    {
        return self::$dumpArray;
    }

    /**
     * Add output data
     * 
     * @param array $data array
     *
     * @return void
     */
    public static function setData($data)
    {
        self::$data[] = $data;
    }

    /**
     * Returns to output data
     * 
     * @return array
     */
    public static function getData()
    {
        return self::$data;
    }

    /**
     * Set error
     * 
     * @param string $error error
     * 
     * @return void
     */
    public static function error($error)
    {
        self::$errors[] = $error;
    }

    /**
     * Check test output has error
     *
     * @return boolean
     */
    public static function hasError()
    {
        return empty(self::$error) ? false : true;
    }

    /**
     * Return to errors
     * 
     * @return array
     */
    public static function getErrors()
    {
        return self::$errors;
    }

    /**
     * Generate single file view
     *
     * @param object $controller controller
     * @param object $container  container
     * 
     * @return string
     */
    public static function generateConsoleFileView($controller, $container)
    {
        echo Console::logo("Welcome to Test Manager (c) 2016");
        echo Console::newline(2);

        $class = $container->get('router')->getClass();
        $class = ucfirst($class);

        echo Console::text($class." Class", "yellow");
        echo Console::newline(2);

        foreach (TestHelper::getClassMethods($controller) as $value) {
            echo Console::text("- ".$value, "yellow");
            echo Console::newline(1);
        }
        echo Console::newline(1);
    }

    /**
     * Generate console view
     * 
     * @param array  $data  data
     * @param string $class class name
     * @param array  $stats stats
     * 
     * @return string
     */
    public static function generateConsoleView($data, $class, $stats)
    {
        echo Console::logo("Welcome to Test Manager (c) 2016");
        echo Console::newline(2);
        echo Console::text("Results of '".$class."'", "yellow");
        echo Console::newline(2);

        foreach (self::getData() as $data) {

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
        echo Console::text($stats['a']."/".$stats['c']." tests complete: ", "yellow", true);
        echo Console::text($stats['p']." passes and ".$stats['f']." fails.", "yellow");
        echo Console::newline(2);
    }
    
}