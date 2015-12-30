<?php

namespace Obullo\Validator;

use RuntimeException;

/**
 * Parse rule parameters
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RuleParameter
{
    /**
     * Strip the parameter (if exists) from the rule
     * Rules can contain parameters: min(5), iban(FR)(false)
     * 
     * @param string $ruleString rule
     * 
     * @return array matches
     */
    public static function parse($ruleString)
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
        return $parts;
    }

} 
