<?php

namespace Obullo\Translation;

use ArrayAccess;
use RuntimeException;
use Obullo\Log\LoggerInterface;
use Obullo\Config\ConfigInterface;

/**
 * Translator Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Translator implements TranslatorInterface
{
    /**
     * System debug translate notice
     */
    const NOTICE = 'translate:';

    /**
     * Config
     * 
     * @var array
     */
    protected $config;

    /**
     * Logger
     *
     * @var object
     */
    protected $logger;

    /**
     * Selected locale
     *
     * @var string
     */
    public $locale = null;

    /**
     * Default locale
     *
     * @var string
     */
    public $default = 'en';

    /**
     * Translation file is loaded
     *
     * @var array
     */
    public $loaded = array();

    /**
     * Current locale code ( en, de, es )
     *
     * @var string
     */
    public $fallback = 'en';

    /**
     * Translate files stack
     *
     * @var array
     */
    public $translateArray = array();

    /**
     * Fallback translate files
     *
     * @var array
     */
    public $fallbackArray = array();

    /**
     * Constructor
     *
     * @param object $config ConfigInterface
     * @param object $logger LoggerInterface
     */
    public function __construct(ConfigInterface $config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config->load('translator');   // Load package config file

        $this->setDefault($this->config['default']['locale']);    // Sets default langugage from translator config file.
        $this->setFallback($this->config['fallback']['locale']);  // Default lang code

        $this->logger->debug('Translator Class Initialized');
    }

    /**
     * Check translation exists.
     *
     * @param string $key translate string
     *
     * @return boolean returns to false if key not exists
     */
    public function exists($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Sets a parameter or an object.
     *
     * @param string $key   The unique identifier for the parameter
     * @param mixed  $value The value of the parameter
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->translateArray[$key] = $value;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return mixed The value of the parameter or an object
     */
    public function offsetGet($key)
    {
        if (! is_string($key)) {
            $this->logger->warning('Translate key type error the key must be string.');
            return $key;
        }
        if (! isset($this->translateArray[$key])) {
            $notice = ($this->config['default']['debug']) ? static::NOTICE : '';

            if ($this->config['fallback']['enabled'] && isset($this->fallbackArray[$key])) {    // Fallback translation is exist ?
                return $this->fallbackArray[$key];      // Get it.
            }
            return $notice.$key;
        }
        return $this->translateArray[$key];
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return Boolean
     */
    public function offsetExists($key)
    {
        return isset($this->translateArray[$key]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $key The unique identifier for the parameter
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->translateArray[$key]);
    }

    /**
     * Load a translation file
     *
     * @param string $filename filename
     * 
     * @return object translator
     */
    public function load($filename)
    {
        $locale = $this->getLocale(); // Get current locale which is set by translation middleware.

        if (empty($locale)) {
            $this->setLocale($this->getDefault());  // Set default translation

            //  Translation code must be set with translator->setLocale() function.
            //  You need to use translation middleware in middlewares.php.

            $locale = $this->getLocale();
        }
        $fileUrl = TRANSLATIONS . $locale.'/'.$filename . '.php';
        $fileKey = substr(strstr($fileUrl, $locale), 0, -4);

        if (in_array($fileKey, $this->loaded, true)) {
            return $this->translateArray;
        }
        static::isDir($locale);

        $translateArray = include $fileUrl;

        if (! isset($translateArray)) {
            $this->logger->error('Translation file does not contain valid format: ' . TRANSLATIONS . $locale .'/'. $filename . '.php');
            return;
        }
        $this->loaded[] = $fileKey;
        $this->logger->debug('Translation file loaded: ' . TRANSLATIONS . $locale .'/'. $filename . '.php');

        $this->translateArray = array_merge($this->translateArray, $translateArray);
        $this->loadFallback($fileKey);  // Load fallback translation if fallback enabled

        unset($translateArray);
        return $this;
    }

    /**
     * Load all fallback files for valid translations
     *
     * @param string $fileKey fallback file
     *
     * @return void
     */
    protected function loadFallback($fileKey)
    {
        if ($this->config['fallback']['enabled']) {
            $locale   = $this->getFallback();
            $filename = ltrim(strstr($fileKey, '/'), '/');
            $fileUrl  = TRANSLATIONS . $locale .'/'. $filename . '.php';
            $fileKey  = substr(strstr($fileUrl, $locale), 0, -4);

            $fallbackArray = include $fileUrl;
            $this->fallbackArray = array_merge($this->fallbackArray, $fallbackArray);
        }
    }

    /**
     * Check language directory exists
     *
     * @param string $locale folder
     *
     * @return boolean
     */
    protected static function isDir($locale)
    {
        if (! is_dir(TRANSLATIONS . $locale)) {
            throw new RuntimeException(
                sprintf(
                    'The translator %s path is not a folder.',
                    TRANSLATIONS . $locale
                )
            );
        }
    }

    /**
     * Gets a parameter or an object.
     *
     * @return mixed The value of the parameter or an object
     */
    public function get()
    {
        $count = func_num_args();
        $args  = func_get_args();

        $item = $args[0];
        if (strpos($item, 'translate:') === 0) {    // Do we need to translate the message ?
            $item = substr($item, 10);              // Grab the variable
        }
        if (isset($this->translateArray[$item])) {
            if ($count > 1) {
                unset($args[0]);
                return vsprintf($this->translateArray[$item], $args);
            }
            return $this->translateArray[$item];
        }
        $translateNotice = ($this->config['default']['debug']) ? static::NOTICE : '';
        return $translateNotice . $item;  // Let's notice the developers this line has no translate text
    }

    /**
     * Get translator class cookie
     *
     * @return string
     */
    public function getCookie()
    {
        $name = $this->config['cookie']['name'];
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * Set locale name
     *
     * @param string  $locale      language ( en, es )
     * @param boolean $writeCookie write cookie on / off
     *
     * @return boolean
     */
    public function setLocale($locale = null, $writeCookie = true)
    {
        if (! isset($this->config['languages'][$locale])) {    // If its not in defined languages.
            return false;
        }
        $this->locale = $locale;
        if ($writeCookie) {
            $this->setCookie();  // Write to cookie
        }
        return true;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets default locale
     *
     * @param string $locale ( en, de, tr .. )
     *
     * @return void
     */
    public function setDefault($locale)
    {
        return $this->default = $locale;
    }

    /**
     * Returns to default locale ( en )
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the fallback locale being used.
     *
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set the fallback locale being used.
     *
     * @param string $fallback locale name
     *
     * @return void
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Write to cookies
     *
     * @return void
     */
    public function setCookie()
    {
        if (defined('STDIN')) {  // Disable command line interface errors
            return;
        }
        setcookie(
            $this->config['cookie']['name'], 
            $this->getLocale(), time() + $this->config['cookie']['expire'],
            $this->config['cookie']['path'], 
            $this->config['cookie']['domain'], 
            0
        );
    }
}