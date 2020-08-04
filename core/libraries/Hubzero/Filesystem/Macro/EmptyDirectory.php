<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Macro;

/**
 * Filesystem macro for emptying a directory.
 */
class EmptyDirectory extends Base
{
	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod()
	{
		return 'emptyDirectory';
	}

	/**
	 * Empty a directory's contents.
	 *
	 * @param   string  $dirname
	 * @return  void
	 */
	public function handle($dirname)
	{
		$listing = $this->filesystem->listContents($dirname, false);

		foreach ($listing as $item)
		{
			if ($item['type'] === 'dir')
			{
				$this->filesystem->deleteDirectory($dirname . $item['path']);
			}
			else
			{
				$this->filesystem->delete($dirname . $item['path']);
			}
		}
	}
}
