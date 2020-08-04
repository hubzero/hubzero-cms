<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

/**
 * Helper for calling HTML Grid methods.
 */
class Grid extends AbstractHelper
{
	/**
	 * Display the editor area.
	 *
	 * @param   string  $name  The control name.
	 * @return  string
	 */
	public function __invoke($method)
	{
		$args = func_get_args();

		if (!count($args))
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No arguments passed.');
		}

		$method = array_shift($args);

		return call_user_func_array(array('\\Hubzero\\Html\\Builder\\Grid', $method), $args);
	}
}
