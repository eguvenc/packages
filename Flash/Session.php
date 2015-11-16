<?php

namespace Obullo\Flash;

use Obullo\Log\LoggerInterface;
use Obullo\Config\ConfigInterface;
use Obullo\Session\SessionInterface;
use Obullo\Container\ContainerInterface;

/**
 * Flash Session
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Session
{
    /**
     * Message key
     */
    const MESSAGE = 'message';
    /**
     * Status constants
     */
    const NOTICE_ERROR = 'error';
    const NOTICE_SUCCESS = 'success';
    const NOTICE_WARNING = 'warning';
    const NOTICE_INFO = 'info';

    /**
     * Flash notification config
     * 
     * @var array
     */
    protected $notification = array();

    /**
     * Session
     * 
     * @var object
     */
    protected $session;

    /**
     * Notice keys
     * 
     * @var array
     */
    protected $notice = array();

    /**
     * Flashdata key
     * 
     * @var string
     */
    protected $flashdataKey = 'flash_';

    /**
     * Constructor
     *
     * @param object $c       \Obullo\Container\ContainerInterface
     * @param object $config  \Obullo\Config\ConfigInterface
     * @param object $logger  \Obullo\Log\LoggerInterface
     * @param object $session \Obullo\Session\SessionInterface
     */
    public function __construct(ContainerInterface $c, ConfigInterface $config, LoggerInterface $logger, SessionInterface $session) 
    {
        $this->c = $c;
        $this->session = $session;
        $this->notification = $config->load('notification')['flash'];

        $this->flashdataSweep();  // Delete old flashdata (from last request)
        $this->flashdataMark();   // Marks all new flashdata as old (data will be deleted before next request)
        
        $logger->debug('Session Flash Class Initialized');
    }

    /**
     * Retrieves message template
     * 
     * @param string $message flash string
     * @param string $key     flash config key
     * 
     * @return string message with template
     */
    public function template($message, $key = 'error')
    {
        return str_replace(
            array('{class}','{icon}','{message}'), 
            array($this->notification[$key]['class'], $this->notification[$key]['icon'], $message),
            $this->notification[static::MESSAGE]
        );
    }

    /**
     * Get all outputs of the flash session
     * 
     * @return array
     */
    public function outputArray() 
    {
        $messages = array();
        foreach (array('success', 'error', 'info', 'warning') as $key) {
            $message = $this->get('notice:'.$key);
            if (! empty($message)) {
                $messages[] = $this->template($message, $key);
            }
        }
        return $messages;
    }

    /**
     * Get all outputs of the flash session as *string
     *
     * @param string $newline break tag
     * 
     * @return string
     */
    public function output($newline = '<br />')
    {
        $array = $this->outputArray();
        return implode($newline, $array);
    }

    /**
     * Success flash message
     * 
     * @param string $message notice
     *
     * @return object
     */
    public function success($message)
    {
        $this->set(array('notice:success' => $message, 'notice:status' => static::NOTICE_SUCCESS));
        return $this;
    }

    /**
     * Error flash message
     * 
     * @param string $message notice
     *
     * @return object
     */
    public function error($message)
    {
        $this->set(array('notice:error' => $message, 'notice:status' => static::NOTICE_ERROR));
        return $this;
    }

    /**
     * Info flash message
     * 
     * @param string $message notice
     *
     * @return object
     */
    public function info($message)
    {
        $this->set(array('notice:info' => $message, 'notice:status' => static::NOTICE_INFO));
        return $this;
    }

    /**
     * Warning flash message
     * 
     * @param string $message notice
     *
     * @return object
     */
    public function warning($message)
    {
        $this->set(array('notice:warning' => $message, 'notice:status' => static::NOTICE_WARNING));
        return $this;
    }

    /**
     * Add or change flashdata, only available
     * until the next request
     *
     * @param mixed  $newData key or array
     * @param string $newval  value
     * 
     * @return object
     */
    public function set($newData = array(), $newval = '')
    {
        if (is_string($newData)) {
            $newData = array($newData => $newval);
        }
        if (is_array($newData) && sizeof($newData) > 0) {
            foreach ($newData as $key => $val) {
                $flashdataKey = $this->flashdataKey . ':new:' . $key;
                $this->session->set($flashdataKey, $val);
            }
        }
        return $this;
    }

    /**
     * Fetch a specific flashdata item from the session array
     *
     * @param string $key    you want to fetch
     * @param string $prefix html open tag
     * @param string $suffix html close tag
     * 
     * @return string
     */
    public function get($key, $prefix = '', $suffix = '')
    {
        $flashdataKey = $this->flashdataKey . ':old:' . $key;
        $value = $this->session->get($flashdataKey);
        if ($value == '') {
            $prefix = '';
            $suffix = '';
        }
        return $prefix . $value . $suffix;
    }

    /**
     * Keeps existing flashdata available to next request.
     *
     * @param string $key session key
     * 
     * @return object
     */
    public function keep($key)
    {
        $old_flashdataKey = $this->flashdataKey . ':old:' . $key;
        $value = $this->session->get($old_flashdataKey);
        $new_flashdataKey = $this->flashdataKey . ':new:' . $key;
        $this->session->set($new_flashdataKey, $value);
        return $this;
    }

    /**
     * Identifies flashdata as 'old' for removal
     * when flashdataSweep() runs.
     * 
     * @return void
     */
    public function flashdataMark()
    {
        $session = $this->session->getAll();

        foreach ($session as $name => $value) {
            $parts = explode(':new:', $name);
            if (is_array($parts) && count($parts) === 2) {
                $newName = $this->flashdataKey . ':old:' . $parts[1];
                $this->session->set($newName, $value);
                $this->session->remove($name);
            }
        }
    }

    /**
     * Removes all flashdata marked as 'old'
     *
     * @return void
     */
    public function flashdataSweep()
    {
        $session = $this->session->getAll();
        
        foreach ($session as $key => $value) {
            $value = null;
            if (strpos($key, ':old:')) {
                $this->session->remove($key);
            }
        }
    }

    /**
     * Return to requested container object
     * 
     * @param string $cid class id
     * 
     * @return object
     */
    public function __get($cid)
    {
        return $this->c[$cid];
    }

}