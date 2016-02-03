<?php

namespace Obullo\Validator;

use RuntimeException;

/**
 * Rule
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Rule
{
    /**
     * Rules
     * 
     * @var array
     */
    protected $rules = array();

    /**
     * Rule parameters
     * 
     * @var array
     */
    protected $params = array();

    /**
     * Set validation rules
     * 
     * @param array $rules rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Get next rule
     * 
     * @return string
     */
    public function getNext()
    {
        return array_shift($this->rules);
    }

    /**
     * Set rule arguments e.g. min(5)|max(500)
     * 
     * @param array $params rule arguments
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Returns to rule arguments
     * 
     * @return array|boolean
     */
    public function getParams()
    {
        return isset($this->params[0]) ? $this->params : false;
    }

    /**
     * Get rule argument
     * 
     * @param int     $n      number
     * @param boolean $return 
     * 
     * @return integer|string
     */
    public function getParam($n, $return = false)
    {
        return isset($this->params[$n]) ? $this->params[$n] : $return;
    }

    /**
     * Strip the parameter (if exists) from the rule
     * Rules can contain parameters: min(5), iban(FR)(false)
     * 
     * @param string $ruleString rule
     * 
     * @return array matches
     */
    public function parse($ruleString)
    {
        $ruleString = substr(str_replace(')(', '|', $ruleString), 0, -1);  // convert iban(FR)(false) to 'iban(FR|false)';
        $parts = explode('(', $ruleString);
        $parts[1] = explode('|', $parts[1]);

        if (empty($parts[0]) || empty($parts[1])) {
            throw new RuntimeException(
                sprintf(
                    "The rule string %s could not be parsed.",
                    $ruleString
                )
            );
        }
        $this->setParams($parts[1]); // rule params
        return $parts[0];  // rule name
    }

} 
