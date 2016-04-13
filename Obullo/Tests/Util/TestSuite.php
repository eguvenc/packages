<?php

namespace Obullo\Tests\Util;

use Obullo\Cli\Console;
use Obullo\Tests\Util\Directory;
use Obullo\Utils\Process\Process;

/**
 * Test suite console helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class TestSuite
{
    /**
     * Test file
     * 
     * @param string $class class path
     * 
     * @return void
     */
    public static function fileTest($class)
    {
        $cmd = "php public/index.php $class/?suite=true";
        $process = new Process($cmd, ROOT, null, null, 0);
        $process->run();

        $object = json_decode($process->getOutput(), true);

        if (! $process->isSuccessful()) {

            if ($object != null && is_array($object)) {
                $errorText = "Exception: ".$object['message']."\nFile: ".$object['file']." Line: ".$object['line'];
                echo Console::text($errorText, "red");
                echo Console::newline(1);
            }
            return;
        } else {
            if (empty($object['methods']) || empty($object['class'])) { 

                if (! empty($object['message'])) {
                    $errorText = "Exception: ".$object['message']."\nFile: ".$object['file']." Line: ".$object['line'];
                    echo Console::text($errorText, "red");
                    echo Console::newline(2);
                    return;
                }
                echo Console::text("File not found.", "red");
                echo Console::newline(2);
                return;
            }
            $a = 0; $p = 0; $f = 0; $e = 0;
            foreach ($object['methods'] as $method) {
                $cmd = "php public/index.php $class/$method?suite=true";
                $process = new Process($cmd, ROOT, null, null, 0);
                $process->run();

                $result = json_decode($process->getOutput(), true);
                echo Console::text($object['class']."/".$method." ... ", "yellow");

                if (! $process->isSuccessful() || ! empty($result['message'])) {
                    $e = $e + 1;
                    $errorText = "Exception: ".$result['message']."\nFile: ".$result['file']." Line: ".$result['line'];
                    echo Console::text($errorText, "red");
                }
                $m = json_decode($process->getOutput(), true); // {"assertions":1,"passes":1,"failures":0}

                if (! empty($m["assertions"])) {
                    $a += $m["assertions"];
                    $p += $m["passes"];
                    $f += $m["failures"];

                    echo Console::text("pass:". $m["passes"]." ", "green");
                    echo Console::text("fail:". $m["failures"]." ", "red");
                }
                echo Console::newline(1);
            }
            echo Console::text("--------------------------------------------- ", "yellow");
            echo Console::newline(1);
            echo Console::text("Total    : ".$a." ", "yellow");
            echo Console::newline(1);
            echo Console::text("Passes   : $p ", "yellow");
            echo Console::newline(1);
            echo Console::text("Failures : $f ", "yellow");
            echo Console::newline(1);
            echo Console::text("Exceptions : $e ", "yellow");
            echo Console::newline(2);

            if ($e == 0 && $f == 0) {
                echo Console::text("Tests finished successfuly.", "green");
            } else {
                echo Console::text("Tests finished with failures.", "red");
            }
            echo Console::newline(2);
        }
    }

    /**
     * Test folder
     * 
     * @param string $folder folder
     * 
     * @return void
     */
    public static function folderTest($folder)
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);

        if (empty($folder)) {
            echo Console::fail("Folder name required.");
            echo Console::newline(1);
            return;
        }

        $folder = trim($folder, "/");         // Kill extra slash
        if (! is_dir(FOLDERS .$folder)) {
            echo Console::fail("Test folder not found.");
            echo Console::newline(1);
            return;
        }
        $paths = Directory::scan($folder);
        if ($paths == false) {
            echo Console::fail("The path '".$folder."' is not a valid folder.");
            return;
        }

        $ta = 0; $tp = 0; $tf = 0; $e = 0;
        $pathHasError = array();
        foreach ($paths as $path) {

            $cmd = "php public/index.php $folder/$path/?suite=true";
            $process = new Process($cmd, ROOT, null, null, 0);
            $process->run();

            if (! $process->isSuccessful()) {
                $error = json_decode($process->getOutput(), true);
                if ($error != null && is_array($error)) {
                    $errorText = "Exception: ".$error['message']."\nFile: ".$error['file']." Line: ".$error['line'];
                    echo Console::text($errorText, "red");
                    echo Console::newline(1);
                }
                $pathHasError[$path] = 1;
                $e = $e + 1;
            }
            $object = json_decode($process->getOutput(), true);

            if (isset($object['disabled']) && $object['disabled']) {
                echo Console::text($object['class']." ... ", "yellow");
                echo Console::text("Disabled. ", "red");
                echo Console::newline(1);
                continue;
            }

            if (empty($object['methods']) || empty($object['class'])) {
                continue;
            }
            echo Console::text($object['class']." ... ", "yellow");

            $a = 0; $p = 0; $f = 0;
            foreach ($object['methods'] as $method) {

                $cmd = "php public/index.php $folder/$path/$method?suite=true";
                $process = new Process($cmd, ROOT, null, null, 0);
                $process->run();

                if (! $process->isSuccessful()) {
                    $error = json_decode($process->getOutput(), true);
                    if ($error != null && is_array($error)) {
                        $errorText = "Exception: ".$error['message']."\nFile: ".$error['file']." Line: ".$error['line'];
                        echo Console::text($errorText, "red");
                        echo Console::newline(1);
                    }
                    $e = $e + 1;
                    $pathHasError[$path] = 1;
                }
                // {"assertions":1,"passes":1,"failures":0}
            
                $method = json_decode($process->getOutput(), true);

                if (! empty($method['message'])) {
                    $e = $e + 1;
                    $errorText = "Exception: ".$method['message']."\nFile: ".$method['file']." Line: ".$method['line'];
                    echo Console::text($errorText, "red");
                    echo Console::newline(1);
                }
                if (! empty($method['assertions'])) {
                    $a += $method["assertions"];
                    $p += $method["passes"];
                    $f += $method["failures"];
                }
            }
            $ta += $a;
            $tp += $p;
            $tf += $f;
            if (empty($pathHasError[$path])) {
                echo Console::text("pass: $p ", "green");
                echo Console::text("fail: $f ", "red");
                echo Console::newline(1);  
            }
        }
        // end foreach

        echo Console::text("--------------------------------------------- ", "yellow");
        echo Console::newline(1);
        echo Console::text("Total    : $ta ", "yellow");
        echo Console::newline(1);
        echo Console::text("Passes   : $tp ", "yellow");
        echo Console::newline(1);
        echo Console::text("Failures : $tf ", "yellow");
        echo Console::newline(1);
        echo Console::text("Exceptions : $e", "yellow");

        echo Console::newline(1);
        echo Console::text("--------------------------------------------- ", "yellow");
        echo Console::newline(1);

        if ($e == 0 && $tf == 0) {
            echo Console::text("Tests finished successfuly.", "green");
        } else {
            echo Console::text("Tests finished with failures.", "red");
        }
        echo Console::newline(2);
    
    } // end func.

}