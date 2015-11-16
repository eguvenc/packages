<?php

namespace Obullo\Validator;

use Obullo\Log\LoggerInterface;
use Obullo\Config\ConfigInterface;

/**
 * Alpha Class
 * 
 * @category  Validator
 * @package   Validator
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 * @link      http://obullo.com/package/validator
 */
class Alpha
{
    /**
     * Config
     * 
     * @var object
     */
    protected $config;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Constructor
     * 
     * @param object $config \Obullo\Config\ConfigInterface
     * @param object $logger \Obullo\Log\LoggerInterface
     */
    public function __construct(ConfigInterface $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Alpha
     * 
     * @param string $str  string
     * @param string $lang "L" for all, or "Latin", "Arabic", "Old_Turkic" 
     * 
     * @see http://php.net/manual/en/regexp.reference.unicode.php
     *
     * @return bool
     */         
    public function isValid($str, $lang)
    {
        if (empty($lang)) {
            $lang = 'L';    // auto
        }
        if (defined('PCRE_VERSION') && intval(PCRE_VERSION) < 7) {
            $this->logger->notice('Unicode support disabled your PCRE_VERSION must be >= 7.');
            return ( ! preg_match("/^([-a-z0-9_\-])+$/i", $str)) ? false : true;
        }
        return ( ! preg_match('/^[\p{'.$lang.'}_\-\d]+$/u', $str)) ? false : true;
    }
}