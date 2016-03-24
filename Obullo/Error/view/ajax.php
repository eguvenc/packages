<?php

use Obullo\Error\Utils;

if (isset($fatalError)) {
    echo "Fatal Error\n";
    // We could not load error libraries when error is fatal.
    echo str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, FOLDERS, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'FOLDERS/', 'VENDOR/'),
        $e->getMessage()
    )."\n";
    echo str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, FOLDERS, VENDOR),
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'FOLDERS/', 'VENDOR/'),
        $e->getFile()
    ) . ' Line : ' . $e->getLine()."\n";
    exit;
}
echo "Exception Error\n". Utils::securePath($e->getMessage())."\n";

if (isset($lastQuery) && ! empty($lastQuery)) {
    echo 'SQL: ' . $lastQuery . "\n";
}
echo $e->getCode().' '.Utils::securePath($e->getFile()). ' Line : ' . $e->getLine() . "\n";

echo "Details: \n";
$fullTraces  = $e->getTrace();
$debugTraces = array();

global $container;

$fatalErrors = [
    E_ERROR,
    E_COMPILE_ERROR,
    E_CORE_ERROR,
    E_RECOVERABLE_ERROR
];

if ($container->has('config') 
    && $container->get('config')['extra']['debugger'] == false
    && $container->get('config')['extra']['debug_backtrace'] == true
) {  // disable backtrace if websocket enabled otherwise we get memory error.

    if (! in_array($e->getCode(), $fatalErrors)) {
        foreach ($fullTraces as $key => $val) {
            if (isset($val['file']) && isset($val['line'])) {
                $debugTraces[] = $val;
            }
        }
    }
}
if (! empty($debugTraces) && isset($debugTraces[0]['file']) && isset($debugTraces[0]['line'])) {

    if (isset($debugTraces[1]['file']) && isset($debugTraces[1]['line'])) {    
        $output = '';
        $i = 0;
        foreach ($debugTraces as $key => $trace) {
            ++$i;
            if (isset($trace['file']) && $i == 1) { // Just show the head class path
                $output = '';
                if (isset($trace['class']) && isset($trace['function'])) {
                    $output.= $trace['class'] . '->' . $trace['function'];
                }
                if (! isset($trace['class']) && isset($trace['function'])) {
                    $output.= $trace['function'];
                }
                $output.= (isset($trace['function'])) ? '()' : '';
                echo $output;
            }

            ++$key;
            
            if ($i == 1)  // Just show the head file
            echo "\n".Utils::securePath($trace['file']).' Line : ' . $trace['line'] . "\n";

        } // end foreach 

        echo "\n";

    }   // end if isset debug traces
}   // end if isset 