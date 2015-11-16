<?php

use Obullo\Error\Utils;

if (isset($fatalError)) {
    echo "\33[1;31mFatal Error\33[0m\n";
    // We could not load error libraries when error is fatal.
    
    echo "\33[0;31m".str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
        $e->getMessage()
    )."\33[0m\n";
    echo "\33[0;31m".str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR),
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
        $e->getFile()
    ) . ' Line : ' . $e->getLine()."\33[0m\n";
    exit;
}
echo "\33[1;31mException Error\n". Utils::securePath($e->getMessage())."\33[0m\n";

if (isset($lastQuery) && ! empty($lastQuery)) {
    echo "\33[0;31m".'SQL: ' . $lastQuery . "\33[0m\n";
}
echo "\33[0;31m".$e->getCode().' '.Utils::securePath($e->getFile()). ' Line : ' . $e->getLine() ."\33[0m\n";

if (isset($eTrace)) {
    echo "\33[0;31m".strip_tags(Utils::debugFileSource($eTrace))."\33[0m";
}

$fullTraces  = $e->getTrace();
$debugTraces = array();

foreach ($fullTraces as $key => $val) {
    if (isset($val['file']) && isset($val['line'])) {
        $debugTraces[] = $val;
    }
}
if (isset($debugTraces[0]['file']) && isset($debugTraces[0]['line'])) {

    echo "\33[1;31mDetails: \33[0m\n\33[0;31m";

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

    }   // end if isset debug traces
    
}   // end if isset 

echo "\33[0m";