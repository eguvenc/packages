<?php

namespace Obullo\Filters;

use Interop\Container\ContainerInterface as Container;

/**
 * Filter controller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Filter
{
	/**
	 * Container
	 * 
	 * @var object
	 */
	protected $container;

	/**
	 * Constructor
	 * 
	 * @param Container $container container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Class loader
	 * 
	 * @param string $class name
	 * 
	 * @return object | null
	 */
	public function __get($class)
	{
		return $this->container->get('filter.'.strtolower($class)); // Call services: $this->filter->is->int(), $this->filter->clean->int();
	}
}