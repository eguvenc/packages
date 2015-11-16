<?php

namespace Obullo\Container;

/**
 * Abstract Service Connnection Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AbstractServiceProvider
{
    /**
     * Connection id prefix
     * 
     * @var string
     */
    protected $connPrefix;

    /**
     * Connection ids
     * 
     * @var array
     */
    protected $connections = array();

    /**
     * Sets container connection prefix
     * 
     * @param string $prefix connection prefix
     *
     * @return void
     */
    public function setKey($prefix)
    {
        $this->connPrefix = $prefix;
    }

    /**
     * Returns to connection prefix
     *
     * E.g. amqp.connection.
     * 
     * @param null|integer $id connection id
     * 
     * @return string
     */
    public function getKey($id = null)
    {
        return ($id == null) ? $this->connPrefix : $this->connPrefix.$id;
    }

    /**
     * Returns to connection id
     * 
     * @param string $string serialized parameters
     * 
     * @return integer
     */
    public function getConnectionId($string)
    {
        $connid = sprintf("%u", crc32(serialize($string)));
        $this->connections[$this->connPrefix][] = $this->connPrefix.$connid;
        return $connid;
    }

    /**
     * Returns all connections
     * 
     * @return array
     */
    public function getFactoryConnections()
    {
        return $this->connections[$this->connPrefix];
    }
}