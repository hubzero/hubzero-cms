<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

class MockProxy
{

	protected $class;

	public function __construct($args = [])
	{
		$this->class = $args['class'];
	}

	public function __call($name, $args)
	{
		$class = $this->class;

		$result = $class::$name(...$args);

		return $result;
	}

}
