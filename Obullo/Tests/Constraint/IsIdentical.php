<?php

namespace Obullo\Tests\Constraint;

/*
 * This file borrowed from PHPUnit framework.
 */

/**
 * Constraint that asserts that one value is identical to another.
 *
 * Identical check is performed with PHP's === operator, the operator is
 * explained in detail at
 * {@url http://www.php.net/manual/en/types.comparisons.php}.
 * Two values are identical if they have the same value and are of the same
 * type.
 *
 * The expected value is passed in the constructor.
 *
 * @since Class available since Release 3.0.0
 */
class IsIdentical
{
    /**
     * @var float
     */
    const EPSILON = 0.0000000001;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return mixed
     */
    public function matches($other)
    {
        if (is_double($this->value) && is_double($other)
            && ! is_infinite($this->value) 
            && ! is_infinite($other)
            && ! is_nan($this->value) 
            && ! is_nan($other)
        ) {
            $success = abs($this->value - $other) < self::EPSILON;
        } else {
            $success = $this->value === $other;
        }
        return $success;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other Evaluated value or object.
     *
     * @return string
     */
    protected function failureDescription($other)
    {
        if (is_object($this->value) && is_object($other)) {
            return 'two variables reference the same object';
        }

        if (is_string($this->value) && is_string($other)) {
            return 'two strings are identical';
        }
        return var_export($other, true) . ' ' . $this->toString();

    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        if (is_object($this->value)) {
            return 'is identical to an object of class "' .
                   get_class($this->value) . '"';
        } else {
            return 'is identical to ' .
                   var_export($this->value, true);
        }
    }
}