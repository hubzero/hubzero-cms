<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Hubzero class for manipulating and reading the filesystem
 */
class Flysystem extends \League\Flysystem\Filesystem
{
	/**
	 * Gets the directory contents
	 *
	 * @param   string  $directory  The subdirectory to access within the filesystem root
	 * @param   bool    $recursive  Whether or not to iterate down recursively
	 * @return  \Hubzero\Filesystem\Collection
	 **/
	public function listContents($directory = '', $recursive = false)
	{
		$contents = parent::listContents($directory, $recursive);

		return $this->encapsulate($contents);
	}

	/**
	 * Encapsulates the entities list in their appropriate classes and returns as part of a collection
	 *
	 * @param   array  $entities  The filesystem contents
	 * @return  \Hubzero\Filesystem\Collection
	 */
	private function encapsulate($entities)
	{
		$items = [];

		foreach ($entities as $entity)
		{
			$items[] = Entity::fromMetadata($entity, $this);
		}

		return new Collection($items);
	}
}
