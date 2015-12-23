<?php

namespace Obullo\Utils;

/**
 * CaseInsentiveArray Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * Container
     * 
     * @var array
     */
    public $container = array();

    /**
     * Set value
     * 
     * @param string $offset key
     * @param string $value  value
     * 
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
            if (!($index === false)) {
                $keys = array_keys($this->container);
                unset($this->container[$keys[$index]]);
            }
            $this->container[$offset] = $value;
        }
    }

    /**
     * Check key exists
     * 
     * @param string $offset key
     * 
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists(strtolower($offset), array_change_key_case($this->container, CASE_LOWER));
    }

    /**
     * Unset value
     * 
     * @param string $offset key
     * 
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Get value
     * 
     * @param string $offset key
     * 
     * @return string
     */
    public function offsetGet($offset)
    {
        $index = array_search(strtolower($offset), array_keys(array_change_key_case($this->container, CASE_LOWER)));
        if ($index === false) {
            return null;
        }
        $values = array_values($this->container);
        return $values[$index];
    }

    /**
     * Count values
     * 
     * @return integer
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * Get current value
     * 
     * @return string
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * Get next value
     * 
     * @return string
     */
    public function next()
    {
        return next($this->container);
    }

    /**
     * Returns current key
     * 
     * @return string
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * Returns to valid data
     * 
     * @return string
     */
    public function valid()
    {
        return !($this->current() === false);
    }

    /**
     * Reset container
     * 
     * @return void
     */
    public function rewind()
    {
        reset($this->container);
    }
}