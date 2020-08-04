<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Type;

use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\Entity;

/**
 * Zip model
 */
class Zip extends Expandable
{
	/**
	 * Expand archive
	 *
	 * @param   bool  $cleanup  Whether or not to clean up after expansion (i.e. removing known OS files, etc...)
	 * @return  bool
	 */
	public function expand($cleanup = true)
	{
		// Create local tmp copy of the archive that's being expanded
		$temp = Manager::getTempPath($this->getName());
		$this->copy($temp);

		$zip = new \ZipArchive;

		// Open the temp archive (we use the absolute path because we're on the local filesystem)
		if ($zip->open($temp->getAbsolutePath()) === true)
		{
			// We don't actually have to extract the archive, we can just read out of it and copy over to the original location
			for ($i = 0; $i < $zip->numFiles; $i++)
			{
				$filename = $zip->getNameIndex($i);
				$entity   = Entity::fromPath($this->getParent() . '/' . $filename, $this->getAdapter());

				if ($entity->isFile())
				{
					// Open
					$item = fopen('zip://' . $temp->getAbsolutePath() . '#' . $filename, 'r');

					// Write stream
					$entity->putStream($item);

					// Close
					fclose($item);
				}
				else
				{
					// Create the directory
					$entity->create();
				}
			}

			// Clean up
			$zip->close();
			$temp->delete();

			return parent::expand($cleanup);
		}

		return false;
	}
}
