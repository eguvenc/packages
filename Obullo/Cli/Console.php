<?php

namespace Obullo\Cli;

/**
 * Console Colors
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Console
{
    /**
     * Print logo
     * 
     * @param string $info text
     * 
     * @return string
     */
    public static function logo($info = 'Welcome to Task Manager (c) 2015')
    {
        $logo = "\33[1;31m
                _           _ _       
           ___ | |__  _   _| | | ___  
          / _ \| '_ \| | | | | |/ _ \ 
         | (_) | |_) | |_| | | | (_) |
          \___/|_.__/ \__,_|_|_|\___/  \n\n";

        return $logo.= "       ".$info."\33[1;31m\33[0m";
    }

    /**
     * Print logo bottom message
     * 
     * @param string $text string
     * 
     * @return string message
     */
    public static function description($text)
    {
        return "\n\33[1;31m".$text."\33[1;31m\33[0m\n\n";
    }

    /**
     * Get default text color
     *
     * @param string $text body
     * @param string $bold enable / disable bold text
     * 
     * @return string
     */
    public static function body($text, $bold = false)
    {
        $boldText = 0;
        if ($bold) {
            $boldText = 1;
        }
        return "\33[$boldText;37m".$text."\33[$boldText;37m\33[0m";
    }

    /**
     * Custom text
     * 
     * @param string  $text  text
     * @param string  $color name
     * @param boolean $bold  whether to bold
     * 
     * @return string
     */
    public static function text($text, $color = 'white', $bold = false)
    {
        $bold = ($bold) ? 1 : 0;

        switch ($color) {
        case 'red':
            return "\33[$bold;31m".$text."\33[$bold;31m\33[0m";
            break;
        case 'green':
            return "\33[$bold;32m".$text."\33[$bold;32m\33[0m";
            break;
        case 'yellow':
            return "\33[$bold;33m".$text."\33[$bold;33m\33[0m";
            break;
        case 'blue':
            return "\33[$bold;34m".$text."\33[$bold;34m\33[0m";
            break;
        case 'purple':
            return "\33[$bold;35m".$text."\33[$bold;35m\33[0m";
            break;
        case 'cyan':
            return "\33[$bold;36m".$text."\33[$bold;36m\33[0m";
            break;
        case 'white':
            return "\33[$bold;37m".$text."\33[$bold;37m\33[0m";
            break;
        }
    }

    /**
     * Print new lines
     * 
     * @param integer $n number of n
     * 
     * @return string
     */
    public static function newline($n)
    {
        return str_repeat("\n", $n);
    }

    /**
     * Get help text color
     *
     * @param string $text body
     * @param string $bold enable / disable bold text
     * 
     * @return string
     */
    public static function help($text, $bold = false)
    {
        $boldText = 0;
        if ($bold) {
            $boldText = 1;
        }
        return "\33[$boldText;33m".$text."\33[$boldText;33m\33[0m";
    }

    /**
     * Get default text color
     *
     * @param string $text body
     * 
     * @return string
     */
    public static function success($text)
    {
        return "\33[1;32m".$text."\33[1;32m\33[0m\n";
    }

    /**
     * Get default text color
     *
     * @param string $text body
     * 
     * @return string
     */
    public static function fail($text)
    {
        return "\33[1;31m".$text."\33[1;31m\33[0m\n";
    }

    /**
     * Write foreground coloured texts
     * 
     * @param string $text            text
     * @param string $foregroundColor available colors "red" or "green"
     * 
     * @return string
     */
    public static function foreground($text = '', $foregroundColor = 'red')
    {
        $foregroundColorCode = "\33[41m";
        $color ="\33[1;31m";
        if ($foregroundColor == 'green') {
            $foregroundColorCode = "\33[42m";
            $color ="\33[1;32m";
        }
        return "\33[1;37m$foregroundColorCode".$text."\33[0m".$color;
    }

}