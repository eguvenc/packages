<?php

namespace Obullo\Error;

/**
 * Debugger Utility methods
 */
class Utils
{
    /**
     * Dump arguments
     * 
     * This function borrowed from Kohana Php Framework
     * 
     * @param mixed   $var    variable
     * @param integer $length length
     * @param integer $level  level
     * 
     * @return mixed
     */
    public static function dumpArgument(& $var, $length = 128, $level = 0)
    {
        global $c;
        if ($var === null) {
            return '<small>null</small>';
        } elseif (is_bool($var)) {
            return '<small>bool</small> ' . ($var ? 'true' : 'false');
        } elseif (is_float($var)) {
            return '<small>float</small> ' . $var;
        } elseif (is_resource($var)) {
            if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var)) {
                $meta  = stream_get_meta_data($var);

                if (isset($meta['uri'])) {
                    $file = $meta['uri'];
                    if (stream_is_local($file)) { 
                        $file = static::securePath($file);
                    }
                    return '<small>resource</small><span>(' . $type . ')</span> ' . htmlspecialchars($file, ENT_NOQUOTES, $c['config']['locale']['charset']);
                }
            } else {
                return '<small>resource</small><span>(' . $type . ')</span>';
            }
        } elseif (is_string($var)) {
            // Encode the string
            $str = htmlspecialchars($var, ENT_NOQUOTES, $c['config']['locale']['charset']);

            return '<small>string</small><span>(' . strlen($var) . ')</span> "' . $str . '"';
        } elseif (is_array($var)) {
            $output = array();
            $space  = str_repeat($s = '    ', $level);   // Indentation for this variable

            static $marker;

            if ($marker === null) {
                // Make a unique marker
                $marker = uniqid("\x00");
            }
            if (empty($var)) {
                // Do nothing
            } elseif (isset($var[$marker])) {
                $output[] = "(\n$space$s*RECURSION*\n$space)";
            } elseif ($level < 5) {
                $output[] = "<span>(";

                $var[$marker] = true;
                foreach ($var as $key => & $val) {
                    if ($key === $marker)
                        continue;
                    if (!is_int($key)) {
                        $key = '"' . htmlspecialchars($key, ENT_NOQUOTES, $c['config']['locale']['charset']) . '"';
                    }
                    $output[] = "$space$s$key => " . static::dumpArgument($val, $length, $level + 1);
                }
                unset($var[$marker]);

                $output[] = "$space)</span>";
            } else {
                // Depth too great
                $output[] = "(\n$space$s...\n$space)";
            }

            return '<small>array</small><span>(' . count($var) . ')</span> ' . implode("\n", $output);

        } elseif (is_object($var)) {
            // Copy the object as an array
            $array = (array) $var;

            $output = array();

            // Indentation for this variable
            $space = str_repeat($s = '    ', $level);

            if (function_exists('spl_object_hash')) {
                $hash = spl_object_hash($var);
            } else {
                $hash = uniqid("\x00");
            }

            // Objects that are being dumped
            static $objects = array();

            if (empty($var)) {
                // Do nothing
            } elseif (isset($objects[$hash])) {
                $output[] = "{\n$space$s*RECURSION*\n$space}";
            } elseif ($level < 10) {
                $output[] = "<pre>{";

                $objects[$hash] = true;
                foreach ($array as $key => & $val) {
                    if ($key[0] === "\x00") {
                        // Determine if the access is protected or protected
                        $access = '<small>' . (($key[1] === '*') ? 'protected' : 'private') . '</small>';

                        // Remove the access level from the variable name
                        $key = substr($key, strrpos($key, "\x00") + 1);
                    } else {
                        $access = '<small>public</small>';
                    }

                    $output[] = "$space$s$access $key => " . static::dumpArgument($val, $length, $level + 1);
                }
                unset($objects[$hash]);

                $output[] = "$space}</pre>";
            } else {
                // Depth too great
                $output[] = "{\n$space$s...\n$space}";
            }
            return '<small>object</small> <span class="object">' . get_class($var) . '(' . count($array) . ')</span> ' . implode("<br />", $output);
        } else {
            return '<small>' . gettype($var) . '</small> ' . htmlspecialchars(print_r($var, true), ENT_NOQUOTES, $c['config']['locale']['charset']);
        }
    }

    /**
     * Write File Source
     * This function borrowed from Kohana Php Framework.
     * 
     * @param array   $trace  debug backtrace
     * @param integer $key    div collapse count - on / off
     * @param string  $prefix div id prefix
     * 
     * @return string html
     */
    public static function debugFileSource($trace, $key = 0, $prefix = '')
    {
        global $c;
        $file = $trace['file'];
        $line_number = $trace['line'];

        if (! $file || ! is_readable($file)) {
            return false;   // Continuing will cause errors
        }
        $file = fopen($file, 'r');      // Open the file and set the line position
        $line = 0;

        // Set the reading range
        $range  = array('start' => $line_number - 3, 'end' => $line_number + 3);
        $format = '% ' . strlen($range['end']) . 'd';  // Set the zero-padding amount for line numbers

        $source = '';
        while (($row = fgets($file)) !== false) {
            if (++$line > $range['end'])  // Increment the line number
                break;

            if ($line >= $range['start']) {
                $row = htmlspecialchars($row, ENT_NOQUOTES, $c['config']['locale']['charset']);  // Make the row safe for output
                $row = '<span class="number">' . sprintf($format, $line) . '</span> ' . $row;  // Trim whitespace and sanitize the row
                if ($line === $line_number) {
                    $row = '<span class="line highlight">' . $row . '</span>';  // Apply highlighting to this row
                } else {
                    $row = '<span class="line">' . $row . '</span>';
                }
                $source .= $row;  // Add to the captured source
            }
        }
        fclose($file);  // Close the file
        $display = ($key > 0) ? ' class="collapsed" ' : '';

        return '<div id="error_toggle_' . $prefix . $key . '" ' . $display . '><pre class="source"><code>' . $source . '</code></pre></div>';
    }

    /**
     * Don't show root paths for security
     * reason.
     * 
     * @param string $file        file path
     * @param string $searchPaths replace paths with secure constant names
     * 
     * @return string
     */
    public static function securePath($file, $searchPaths = false)
    {
        if ($searchPaths) {
            $replace = array(
                'APP/',
                'DATA/',
                'CLASSES/',
                'ROOT/',
                'OBULLO/',
                'MODULES/',
                'VENDOR/',
            );
            return str_replace(array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR), $replace, $file);
        }
        if (is_string($file)) {
            if (strpos($file, ROOT) === 0) {
                $file = 'ROOT/' . substr($file, strlen(ROOT));
            }
            if (strpos($file, APP) === 0) {
                $file = 'APP/' . substr($file, strlen(APP));
            }
            if (strpos($file, CLASSES) === 0) {
                $file = 'CLASSES/' . substr($file, strlen(CLASSES));
            }
            if (strpos($file, OBULLO) === 0) {
                $file = 'PACKAGES/' . substr($file, strlen(OBULLO));
            }
            if (strpos($file, MODULES) === 0) {
                $file = 'MODULES/' . substr($file, strlen(MODULES));
            }
            if (strpos($file, VENDOR) === 0) {
                $file = 'VENDOR/' . substr($file, strlen(VENDOR));
            }
        }
        return $file;
    }

 }