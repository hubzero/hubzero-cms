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

namespace Hubzero\Filesystem;

/**
 * Directory model
 */
class Directory extends Entity
{
	/**
	 * Grabs the entity extension
	 *
	 * @return  string
	 **/
	public function getExtension()
	{
		return 'folder';
	}

	/**
	 * Lists directory contents
	 *
	 * @param   bool  $recursive  Whether or not to dive down recursively
	 * @return  \Hubzero\Filesystem\Collection
	 **/
	public function listContents($recursive = false)
	{
		return $this->hasAdapterOrFail()->adapter->listContents($this->getPath(), $recursive);
	}

	/**
	 * Create the directory
	 *
	 * @return  bool
	 **/
	public function create()
	{
		return $this->hasAdapterOrFail()->adapter->createDir($this->getPath());
	}

	/**
	 * Deletes the directory
	 *
	 * @return  bool
	 **/
	public function delete()
	{
		return $this->hasAdapterOrFail()->adapter->deleteDir($this->getPath());
	}
}