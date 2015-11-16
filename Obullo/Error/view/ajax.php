<?php

use Obullo\Error\Utils;

if (isset($fatalError)) {
    echo "Fatal Error\n";
    // We could not load error libraries when error is fatal.
    echo str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
        $e->getMessage()
    )."\n";
    echo str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR),
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
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

foreach ($fullTraces as $key => $val) {
    if (isset($val['file']) && isset($val['line'])) {
        $debugTraces[] = $val;
    }
}
if (isset($debugTraces[0]['file']) && isset($debugTraces[0]['line'])) {

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