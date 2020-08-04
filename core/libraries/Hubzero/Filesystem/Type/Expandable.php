<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Type;

use Hubzero\Filesystem\File;

/**
 * Expandable model
 */
class Expandable extends File
{
	/**
	 * Expand archive
	 *
	 * @param   bool  $cleanup  Whether or not to clean up after expansion (i.e. removing known OS files, etc...)
	 * @return  bool
	 */
	public function expand($cleanup = true)
	{
		if ($cleanup)
		{
			return $this->cleanup();
		}

		return true;
	}

	/**
	 * Cleans the archive of OS-specific files
	 *
	 * @return  bool
	 **/
	protected function cleanup()
	{
		$items = $this->getParent(false)->listContents();

		foreach ($items as $item)
		{
			if (in_array($item->getName(), ['.svn', 'CVS', '.DS_Store', '__MACOSX']))
			{
				if (!$item->delete())
				{
					return false;
				}
			}
		}

		return true;
	}
}
