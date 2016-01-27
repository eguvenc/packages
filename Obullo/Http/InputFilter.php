<?php

namespace Obullo\Http;

use Obullo\Container\ImmutableContainerAwareInterface;
use Obullo\Container\ImmutableContainerAwareTrait;

/**
 * Input Filter
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class InputFilter implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Current input value
     * 
     * @var mixed
     */
    protected $value;

    /**
     * Current filter name
     * 
     * @var string
     */
    protected $filter;

    /**
     * Set filter name
     * 
     * @param string $name filter
     *
     * @return object this
     */
    public function setFilter($name)
    {
        $this->filter = $name;
        return $this;
    }

    /**
     * Get filter object
     * 
     * @return object
     */
    public function getFilter()
    {
        return $this->container->get($this->filter);
    }

    /**
     * Set latest request input value
     * 
     * @param mixed $value value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get input value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Call filter class & execute filter methods
     * 
     * @param string $method    name
     * @param array  $arguments argument array
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (count($arguments) > 0) {
            array_unshift($arguments, $this->getValue());
        } else {
            $arguments = array($this->getValue());
        }
        return call_user_func_array(
            array($this->getFilter(), $method),
            $arguments
        );
    }
}