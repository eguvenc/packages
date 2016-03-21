<?php

namespace Obullo\Cli\Task;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;
use Obullo\Tests\Directory;
use Obullo\Utils\Process\Process;

class Test extends Controller
{
    /**
     * Execute command
     * 
     * @return void
     */
    public function index()
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);
    }

    /**
     * Test class
     * 
     * @param string $class name
     * 
     * @return void
     */
    public function file($class)
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);

        $cmd = "php test.php $class/?suite=true";
        $process = new Process($cmd, ROOT, null, null, 0);
        $process->run();

        $object = json_decode($process->getOutput(), true);

        if (empty($object['methods']) || empty($object['class'])) {
            return;
        }
        $a = 0; $p = 0; $f = 0;
        foreach ($object['methods'] as $method) {

            $cmd = "php test.php $class/$method?suite=true";
            $process = new Process($cmd, ROOT, null, null, 0);
            $process->run();

            // {"assertions":1,"passes":1,"failures":0}
        
            $m = json_decode($process->getOutput(), true);

            echo Console::text($object['class']."/".$method." ... ", "yellow");
            echo Console::text("pass:". $m["passes"]." ", "green");
            echo Console::text("fail:". $m["failures"]." ", "red");
            echo Console::newline(1);

            $a += $m["assertions"];
            $p += $m["passes"];
            $f += $m["failures"];
        }

        echo Console::text("--------------------------------------------- ", "yellow");
        echo Console::newline(1);
        echo Console::text("Total: $a ", "yellow");
        echo Console::text("Passes: $p ", "green");
        echo Console::text("Failures: $f ", "red");
        echo Console::newline(2);
    }

    /**
     * Folders
     * 
     * @param string $folder folder
     * 
     * @return void
     */
    public function folder($folder = null)
    {
        echo Console::logo("Welcome to Test Suite (c) 2016");
        echo Console::newline(2);

        if (empty($folder)) {
            echo Console::fail("Folder name required.");
            echo Console::newline(1);
            return;
        }
        $folder = trim($folder, "/");         // Kill extra slash
        if ($paths = Directory::scan($folder)) {

            $ta = 0; $tp = 0; $tf = 0;
            foreach ($paths as $path) {

                $cmd = "php test.php $folder/$path/?suite=true";
                $process = new Process($cmd, ROOT, null, null, 0);
                $process->run();
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
                    $cmd = "php test.php $folder/$path/$method?suite=true";
                    $process = new Process($cmd, ROOT, null, null, 0);
                    $process->run();

                    // {"assertions":1,"passes":1,"failures":0}
                
                    $method = json_decode($process->getOutput(), true);

                    $a += $method["assertions"];
                    $p += $method["passes"];
                    $f += $method["failures"];
                }
                $ta += $a;
                $tp += $p;
                $tf += $f;
                echo Console::text("pass: $p ", "green");
                echo Console::text("fail: $f ", "red");
                echo Console::newline(1);
            }
            echo Console::text("--------------------------------------------- ", "yellow");
            echo Console::newline(1);
            echo Console::text("Total: $ta ", "yellow");
            echo Console::text("Passes: $tp ", "green");
            echo Console::text("Failures: $tf ", "red");
            echo Console::newline(2);

        } else {
            echo Console::fail("The path '".$folder."' is not a valid folder.");
            echo Console::newline(1);
        }

    }

}