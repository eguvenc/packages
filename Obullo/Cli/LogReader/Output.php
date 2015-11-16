<?php

namespace Obullo\Cli\LogReader;

/**
 * Log outputs
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Output
{
    /**
     * Log message
     * 
     * @var string
     */
    protected $message;

    /**
     * Print current line
     * 
     * @param integer $i    line number
     * @param string  $line line text
     * 
     * @return void
     */
    public function printLine($i, $line)
    {
        if ($i == 0) {
            $line = str_replace("\n", '', $line);
        }
        $this->message = $line = trim(preg_replace('/[\r\n]/', "\n", $line), "\n"); // Remove all newlines   

        $line = $this->writeSQL($line);
        $line = $this->writeHeader($line);
        $line = $this->writeBody($line);

        $this->writeFinalOutput($line);
        $this->writeLevels($line);
    }

    /**
     * Write header
     * 
     * @param string $line line
     * 
     * @return string line
     */
    protected function writeHeader($line)
    {
        $header = $this->has('Request Uri');
        $break = "\n------------------------------------------------------------------------------------------";

        if ($header) {
            $line  = "\033[0;37m".$break."\n".$line.$break."\033[0m";
        }
        if ($this->has('$_')) {
            $line = preg_replace('/\s+/', ' ', $line);
            $line = preg_replace('/\[/', "[", $line);  // Do some cleaning

            if ($this->has('$_LAYER')) {
                $line = "\033[0;37m".strip_tags($line)."\033[0m";
            } else {
                $line = "\033[0;37m".$line."\033[0m";
            }
        }
        return $line;
    }

    /**
     * Write header
     * 
     * @param string $line text
     * 
     * @return string line
     */
    protected function writeBody($line)
    {        
        if ($this->has('$_TASK')) {
            $line = "\033[0;37m".$line."\033[0m";
        }
        if ($this->has('loaded:')) {
            $line = "\033[0;37m".$line."\033[0m";
        }
        return $line;
    }

    /**
     * Write sql
     * 
     * @param string $line line
     * 
     * @return string line
     */
    protected function writeSQL($line)
    {
        if ($this->has('$_SQL')) {   // Remove unnecessary spaces from sql output
            $line = "\033[1;32m".preg_replace('/[\s]+/', ' ', $line)."\033[0m";
            $line = preg_replace('/[\r\n]/', "\n", $line);
        }
        return $line;
    }

    /**
     * Write final response info
     * 
     * @param string $line line
     * 
     * @return void
     */
    protected function writeFinalOutput($line)
    {
        if ($this->has('debug:')) {   // Do not write two times
            if ($this->has('--> Final output sent')) {
                $line = "\033[0m"."\033[0;37m".$line."\033[0m";
            }
            if ($this->has('--> Redirect header')) {
                $line = "\033[0m"."\033[0;37m".$line."\033[0m";
            }
            $line = "\033[0;37m".$line."\033[0m";
            echo $line."\n";
        }
    }

    /**
     * Write log levels
     * 
     * @param string $line line
     * 
     * @return void
     */
    protected function writeLevels($line)
    {
        if ($this->has('info:')) {
            $line = "\033[1;34m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('error:')) {
            $line = "\033[1;31m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('alert:')) {
            $line = "\033[1;31m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('emergency:')) {
            $line = "\033[1;31m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('critical:')) {
            $line = "\033[1;31m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('warning:')) {
            $line = "\033[1;31m".$line."\033[0m";
            echo $line."\n";
        } elseif ($this->has('notice:')) {
            $line = "\033[1;33m".$line."\033[0m";
            echo $line."\n";
        }
    }

    /**
     * Check has colorful level
     * 
     * @param string $string name
     * 
     * @return boolean
     */
    protected function has($string)
    {
        if (strpos($this->message, $string) !== false) {
            return true;
        }
        return false;
    }
}

// Terminal Colour Codes.
/*
$BLACK="33[0;30m";
$DARKGRAY="33[1;30m";
$BLUE="33[0;34m";
$LIGHTBLUE="33[1;34m";
$MAGENTA="33[0;35m";
$CYAN="33[0;36m";
$LIGHTCYAN="33[1;36m";
$RED="33[0;31m";
$LIGHTRED="33[1;31m";
$GREEN="33[0;32m";
$LIGHTGREEN="33[1;32m";
$PURPLE="33[0;35m";
$LIGHTPURPLE="33[1;35m";
$BROWN="33[0;33m";
$YELLOW="33[1;33m";
$LIGHTGRAY="33[0;37m";
$WHITE="33[1;37m";
*/