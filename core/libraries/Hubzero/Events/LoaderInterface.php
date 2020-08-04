<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * Interface for loading event groups.
 */
interface LoaderInterface
{
	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 */
	public function getName();

	/**
	 * Load the given listener group.
	 *
	 * @param   string  $group
	 * @return  array
	 */
	public function loadListeners($group);
}
