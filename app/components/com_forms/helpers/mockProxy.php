<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class MockProxy
{

	/**
	 * Wrapped class
	 *
	 * @var   object
	 */
	protected $_class;

	/**
	 * Constructs MockProxy instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_class = $args['class'];
	}

	/**
	 * Forwards invoked function to wrapped class
	 *
	 * @param    string    $name   Name of invoked function
	 * @param    array     $args   Arguments passed to invoked function
	 * @return   mixed
	 */
	public function __call($name, $args)
	{
		$class = $this->_class;

		$result = $class::$name(...$args);

		return $result;
	}

}
