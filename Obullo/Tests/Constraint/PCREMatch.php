<?php

namespace Obullo\Tests\Constraint;

/*
 * This file borrowed from PHPUnit framework.
 */

/**
 * Constraint that asserts that the string it is evaluated for matches
 * a regular expression.
 *
 * Checks a given value using the Perl Compatible Regular Expression extension
 * in PHP. The pattern is matched by executing preg_match().
 *
 * The pattern string passed in the constructor.
 *
 * @since Class available since Release 3.0.0
 */
class PCREMatch
{
    /**
     * Pattern
     * 
     * @var string
     */
    protected $pattern;

    /**
     * Constructor
     * 
     * @param string $pattern pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    public function matches($other)
    {
        return preg_match($this->pattern, $other) > 0;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'matches PCRE pattern "%s"',
            $this->pattern
        );
    }
}
