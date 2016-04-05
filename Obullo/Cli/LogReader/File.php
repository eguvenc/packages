<?php

namespace Obullo\Cli\LogReader;

use Obullo\Cli\Console;
use Obullo\Container\ContainerAwareTrait;

/**
 * File reader
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class File
{
    use ContainerAwareTrait;

    /**
     * File paths
     * 
     * @var array
     */
    public static $paths = [
        'http'  => '/resources/data/logs/http.log',
        'cli'   => '/resources/data/logs/cli.log',
        'ajax'  => '/resources/data/logs/ajax.log',
    ];

    /**
     * Register log file paths
     * 
     * @param array $paths paths
     *
     * @return void
     */
    public static function setPathArray(array $paths)
    {
        self::$paths = $paths;
    }

    /**
     * Returns to path array
     * 
     * @return array
     */
    public static function getPathArray()
    {
        return static::$paths;
    }

    /**
     * Follow logs
     * 
     * @param string $dir sections ( http, ajax, cli )
     * 
     * @return void
     */
    public function follow($dir = 'http')
    {
        $directions = static::getPathArray();

        if (! isset($directions[$dir])) {
            Console::fail("Path Error: $dir item not defined in ".__CLASS__);
            Console::newline(1);
            exit;
        }
        $file = ROOT .$directions[$dir];

        echo Console::newline(1);
        echo Console::text("Following ".ucfirst($dir)." Log Messages ...", "yellow");
        
        $newline = 1;
        if ($dir == 'cli') {
            $newline = 2;
        }
        echo Console::newline($newline);

        $size = 0;
        while (true) {
            clearstatcache();           // Clear the cache
            if (! file_exists($file)) { // Start process when file exists.
                continue;
            }
            $currentSize = filesize($file); // Continue the process when file size change.
            if ($size == $currentSize) {
                usleep(50);
                continue;
            }
            if (! $fh = fopen($file, 'rb')) {
                Console::fail(
                    "Permission Error: You haven't got a write permission to data folder."
                );
                Console::newline(2);
                die;
            }
            fseek($fh, $size);

            $output = new Output;
            $i = 0;
            while ($line = fgets($fh)) {
                $output->printLine($i, $line);
                $i++;
            }
            fclose($fh);
            clearstatcache();
            $size = $currentSize;
        }
    }

}