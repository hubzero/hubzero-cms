<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Macro;

/**
 * Filesystem macro for listing directories in a tree.
 */
class DirectoryTree extends Base
{
	/**
	 * Tree index.
	 *
	 * @var  int
	 */
	private $index = 0;

	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod()
	{
		return 'directoryTree';
	}

	/**
	 * Lists folder in format suitable for tree display.
	 *
	 * @param   string   $path      The path of the folder to read.
	 * @param   string   $filter    A filter for folder names.
	 * @param   integer  $maxLevel  The maximum number of levels to recursively read, defaults to three.
	 * @param   integer  $level     The current level, optional.
	 * @param   integer  $parent    Unique identifier of the parent folder, if any.
	 * @return  array
	 */
	public function handle($path, $filter = '.', $maxLevel = 3, $level = 0, $parent = 0)
	{
		$dirs = array();

		if ($level == 0)
		{
			$this->index = 0;
		}

		if ($level < $maxLevel)
		{
			$folders = $this->filesystem->listContents($path, $filter);

			// First path, index foldernames
			foreach ($folders as $name)
			{
				if ($name['type'] != 'dir')
				{
					continue;
				}

				$this->index++;

				$fullName = $this->filesystem->cleanPath($path . DS . $name['path']);

				$dirs[] = array(
					'id'       => $this->index,
					'parent'   => $parent,
					'name'     => ltrim($name['path'], '\\/'),
					'fullname' => $fullName,
					'relname'  => str_replace(PATH_ROOT, '', $fullName)
				);

				$dirs2 = $this->handle($fullName, $filter, $maxLevel, $level + 1, $this->index);

				$dirs = array_merge($dirs, $dirs2);
			}
		}

		return $dirs;
	}
}
