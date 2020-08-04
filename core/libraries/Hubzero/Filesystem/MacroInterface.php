<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Filesystem macro interface.
 */
interface MacroInterface
{
	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod();

	/**
	 * Set the Filesystem object.
	 *
	 * @param  $filesystem  Filesystem
	 */
	public function setFilesystem(Filesystem $filesystem);
}
