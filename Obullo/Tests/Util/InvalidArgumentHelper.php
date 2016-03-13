<?php

namespace Obullo\Tests\Util;

use InvalidArgumentException;

/*
 * This file borrowed from PHPUnit framework.
 */

/**
 * Factory for Framework_Exception objects that are used to describe
 * invalid arguments passed to a function or method.
 *
 * @since Class available since Release 3.4.0
 */
class InvalidArgumentHelper
{
    /**
     * Factory
     * 
     * @param int    $argument argument
     * @param string $type     type
     * @param mixed  $value    value
     *
     * @return PHPUnit_Framework_Exception
     */
    public static function factory($argument, $type, $value = null)
    {
        $stack = debug_backtrace(false);

        return new InvalidArgumentException(
            sprintf(
                'Argument #%d%sof %s::%s() must be a %s',
                $argument,
                $value !== null ? ' (' . gettype($value) . '#' . $value . ')' : ' (No Value) ',
                $stack[1]['class'],
                $stack[1]['function'],
                $type
            )
        );
    }
}
