<?php

use Obullo\Error\Utils;

if (isset($fatalError)) {
    echo "\33[1;31mFatal Error\33[0m\n";
    // We could not load error libraries when error is fatal.
    
    echo "\33[0;31m".str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, FOLDERS, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'FOLDERS/', 'VENDOR/'),
        $e->getMessage()
    )."\33[0m\n";
    echo "\33[0;31m".str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, FOLDERS, VENDOR),
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'FOLDERS/', 'VENDOR/'),
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
echo "\33[0m";