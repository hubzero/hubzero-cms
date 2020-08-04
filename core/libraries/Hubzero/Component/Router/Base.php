<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

/**
 * Base component routing class
 */
abstract class Base implements RouterInterface
{
	/**
	 * Generic method to preprocess a URL
	 *
	 * @param   array  $query  An associative array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function preprocess($query)
	{
		return $query;
	}
}
