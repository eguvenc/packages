<?php

namespace Obullo\Utils;

/**
 * File helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class File
{
    /**
     * Don't show root paths for security
     * reason.
     * 
     * @param string $file file path
     * 
     * @return string
     */
    public static function getSecurePath($file)
    {
        $CONSTANTS = [
            'ASSETS',
            'DATA',
            'TRANSLATIONS',
            'CLASSES',
            'TEMPLATES',
            'TASKS',
            'RESOURCES',
            'FOLDERS',
            'OBULLO',
            'VENDOR',
            'APP',
            'ROOT',
        ];
        if (! is_string($file)) {
            return $file;
        }
        foreach ($CONSTANTS as $constant) {
            $value = constant($constant);
            if (strpos($file, $value) === 0) {
                $file = $constant .'/'. substr($file, strlen($value));
            }
        }
        return $file;
    }

    // TESTS:

    // echo \Obullo\Utils\File::getSecurePath(ROOT)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(APP)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(OBULLO)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(VENDOR)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(FOLDERS)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(ASSETS)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(DATA)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(TRANSLATIONS)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(CLASSES)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(TEMPLATES)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(TASKS)."<br>";
    // echo \Obullo\Utils\File::getSecurePath(RESOURCES)."<br>";

}