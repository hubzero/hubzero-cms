<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Type;

use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\Entity;

/**
 * Tar model
 */
class Tar extends Expandable
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

		$archive = new \PharData($temp->getAbsolutePath());

		foreach ($archive as $file)
		{
			// Add 7 to the length for the 'phar://' prefix to the file
			$path   = substr($file, strlen($temp->getAbsolutePath()) + 7);
			$entity = Entity::fromPath($this->getParent() . $path, $this->getAdapter());

			if ($entity->isFile())
			{
				// Open
				$item = fopen($file, 'r');

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
		$temp->delete();

		return parent::expand($cleanup);
	}
}