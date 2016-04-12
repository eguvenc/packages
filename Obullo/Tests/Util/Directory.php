<?php

namespace Obullo\Tests\Util;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Directory scanner
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Directory
{
    /**
     * Scan directory & files
     *
     * @param string $folder folder
     * 
     * @return void
     */ 
    public static function scan($folder)
    {
        if (! is_dir(FOLDERS.$folder)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(FOLDERS.$folder, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $results = array();
        foreach ($iterator as $splFileInfo) {
            $path = $splFileInfo->isDir() ? array($splFileInfo->getFilename() => array()) : array($splFileInfo->getFilename());
            if (in_array(
                $splFileInfo->getFilename(),
                [
                    'views',
                    'fail.php',
                    'index.php',
                    'pass.php',
                    'result.php',
                    'test.php'
                ]
            )) {
                continue;
            }
            for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                $path = array($iterator->getSubIterator($depth)->current()->getFilename() => $path);
            }
            $results = array_merge_recursive($results, $path);
        }
        if (! empty($results)) {
            return self::scanResults($results);
        }
        return false;
    }

    /**
     * Scan results
     * 
     * @param array $folders folders
     * 
     * @return void
     */
    protected static function scanResults(array $folders)
    {
        // print_r($folders);

        $files = array();
        foreach ($folders as $folder => $filename) {

            if (is_array($filename)) {

                $subfolder = "";
                foreach ($filename as $key => $value) {

                    if (! is_numeric($key)) {

                        // Unix directories does not support uppercase characters 
                        // we force to all directory names to lowercase

                        $subfolder = $key;
                        foreach ($value as $k => $file) {

                            if (is_array($file)) {
                                $subfolder = $key;
                                $subfolder = $subfolder.'/'.ucfirst($k);
                                foreach ($file as $v) {
                                    $files[] = strtolower($folder)."/".strtolower($subfolder)."/".substr($v, 0, -4);
                                }

                            } else {
                                $files[] = strtolower($folder)."/".strtolower($subfolder)."/".substr($file, 0, -4);
                            }
                        }

                    } elseif (is_numeric($key)) {

                        $files[] = strtolower($folder)."/".substr($value, 0, -4);
                    }
                }
            } else {  // filename is string

                if ($filename != 'Tests.php') {
                    $files[] = substr($filename, 0, -4);
                }
            }
        }

        // print_r($files);

        return $files;
    }

}