<?php

namespace Obullo\View;

use Closure;
use Obullo\Http\Controller;
use Obullo\Log\LoggerInterface as Logger;
use Obullo\Container\ContainerInterface as Container;

/**
 * View Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class View implements ViewInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Protected variables
     * 
     * @var array
     */
    protected $_boolStack   = array();    // Boolean type view variables
    protected $_arrayStack  = array();    // Array type view variables
    protected $_stringStack = array();    // String type view variables
    protected $_objectStack = array();    // Object type view variables

    /**
     * Constructor
     * 
     * @param object $c      \Obullo\Container\ContainerInterface
     * @param object $logger \Obullo\Log\LoggerInterface
     */
    public function __construct(Container $c, Logger $logger)
    {
        $this->c = $c;
        $this->logger = $logger;
        $this->logger->debug('View Class Initialized');
    }

    /**
     * Get body / write body
     * 
     * @param string  $_Vpath     full path
     * @param string  $_Vfilename filename
     * @param string  $_VData     mixed data
     * @param boolean $_VInclude  fetch as string or include
     * 
     * @return mixed
     */
    public function getBody($_Vpath, $_Vfilename, $_VData = null, $_VInclude = true)
    {
        $_VInclude = ($_VData === false) ? false : $_VInclude;
        $fileExtension = substr($_Vfilename, strrpos($_Vfilename, '.')); // Detect extension ( e.g. '.tpl' )
        $ext = (strpos($fileExtension, '.') === 0) ? '' : '.php';

        $this->assignVariables($_VData);

        extract($this->_stringStack, EXTR_SKIP);
        extract($this->_arrayStack, EXTR_SKIP);
        extract($this->_objectStack, EXTR_SKIP);
        extract($this->_boolStack, EXTR_SKIP);

        ob_start();   // Please open short tags in your php.ini file. ( it must be short_tag = On ).
        include $_Vpath . $_Vfilename . $ext;
        $body = ob_get_clean();
        
        if ($_VData === false || $_VInclude === false) {
            return $body;
        }
        $this->c['response']->getBody()->write($body);
        return;
    }

    /**
     * Assign view variables
     * 
     * @param array $_VData view data
     * 
     * @return void
     */
    protected function assignVariables($_VData)
    {
        if (is_array($_VData)) {
            foreach ($_VData as $key => $value) {
                $this->assign($key, $value);
            }
        }
    }

    /**
     * Set variables
     * 
     * @param mixed $key view key => data or combined array
     * @param mixed $val mixed
     * 
     * @return void
     */
    public function assign($key, $val = null)
    {
        if (is_array($key)) {
            foreach ($key as $_k => $_v) {
                $this->assignVar($_k, $_v);
            }
        } else {
            $this->assignVar($key, $val);
        }
    }

    /**
     * Set variables
     * 
     * @param string $key view key data
     * @param mixed  $val mixed
     * 
     * @return void
     */
    protected function assignVar($key, $val)
    {
        if (is_int($val)) {
            $this->_stringStack[$key] = $val;
            return;
        }
        if (is_string($val)) {
            $this->_stringStack[$key] = $val;
            return;
        }
        $this->_arrayStack[$key] = array();  // Create empty array
        if (is_array($val)) {
            if (count($val) == 0) {
                $this->_arrayStack[$key] = array();
            } else {
                foreach ($val as $array_key => $value) {
                    $this->_arrayStack[$key][$array_key] = $value;
                }
            }
        }
        if (is_object($val)) {
            $this->_objectStack[$key] = $val;
            $this->_arrayStack = array();
            return;
        }
        if (is_bool($val)) {
            $this->_boolStack[$key] = $val;
            $this->_arrayStack = array();
            return;
        }
        $this->_stringStack[$key] = $val;
        $this->_arrayStack = array();
        return;
    }

    /**
     * Include nested view files from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string                      
     */
    public function load($filename, $data = null)
    {
        return $this->renderNestedView($filename, $data, true);
    }

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = null)
    {
        return $this->renderNestedView($filename, $data, false);
    }

    /**
     * Render nested view files
     * 
     * @param string  $filename filename
     * @param mixed   $data     array data
     * @param boolean $include  no include ( fetch as string )
     * 
     * @return string                      
     */
    protected function renderNestedView($filename, $data, $include = true)
    {
        /**
         * IMPORTANT:
         * 
         * Router may not available in some levels, forexample if we define a closure route 
         * which contains view class, it will not work if router not available in the controller.
         * So first we need check Controller is available if not we use container->router.
         */
        if (! class_exists('Obullo\Http\Controller', false) || Controller::$instance == null) {
            $router = $this->c['router'];
        } else {
            $router = &Controller::$instance->router;  // Use nested controller router ( @see the Layer package. )
        }
        /**
         * Fetch view ( also it can be nested )
         */
        $return = $this->getBody(
            MODULES .$router->getModule('/') . $router->getDirectory() .'/view/',
            $filename,
            $data,
            $include
        );
        return $return;
    }

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->c[$key];
    }

}