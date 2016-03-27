<?php

namespace Obullo\Tests\Constraint;

/*
 * This file borrowed from PHPUnit framework.
 */

/**
 * Constraint that asserts that the string it is evaluated for contains
 * a given string.
 *
 * Uses strpos() to find the position of the string in the input, if not found
 * the evaluation fails.
 *
 * The sub-string is passed in the constructor.
 *
 * @since Class available since Release 3.0.0
 */
class StringContains
{
    /**
     * @var string
     */
    protected $string;

    /**
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Constructor
     * 
     * @param string $string     string
     * @param bool   $ignoreCase ignore case
     */
    public function __construct($string, $ignoreCase = false)
    {
        $this->string     = $string;
        $this->ignoreCase = $ignoreCase;
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
        if ($this->ignoreCase) {
            return stripos($other, $this->string) !== false;
        } else {
            return strpos($other, $this->string) !== false;
        }
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        if ($this->ignoreCase) {
            $string = strtolower($this->string);
        } else {
            $string = $this->string;
        }
        return sprintf(
            'contains "%s"',
            $string
        );
    }
}
