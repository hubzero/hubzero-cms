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
 * @package   hubzero-cms
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

require_once \Component::path('com_publications') . '/helpers/zipHelper.php';

use Components\Publications\Models\Publication;
use Components\Publications\Helpers\ZipHelper;
use Hubzero\Utility\Arr;

/*
 * Bundle model
 */
class Bundle
{
	/**
	 * Contents of archive
	 *
	 * @var  array
	 */
	protected $contents = null;

	/**
	 * Instance constructor
	 *
	 * @param   array  $args  Instantiation data
	 * @return  void
	 */
	public function __construct($args)
	{
		$this->publication_id = $args['publication_id'];
		$this->publication_version_id  = $args['publication_version_id'];
	}

	/**
	 * Gets size of corresponding bundle in bytes
	 *
	 * @return  integer
	 */
	public function getSize()
	{
		$path = $this->getWrapperPath();
		$size = null;

		if (file_exists($path))
		{
			$size = filesize($path);
		}

		return $size;
	}

	/**
	 * Gets checksum of corresponding bundle
	 *
	 * @return  string
	 */
	public function getMd5()
	{
		$path = $this->getWrapperPath();
		$checksum = null;

		if (file_exists($path))
		{
			$checksum = md5_file($path);
		}

		return $checksum;
	}

	/**
	 * Builds a hierarchical array of bundle's contents
	 *
	 * @return  array
	 */
	public function getContents()
	{
		if (!isset($this->contents))
		{
			$path = $this->getWrapperPath();
			$this->contents = ZipHelper::getArchiveContents($path);
			$this->expandBundleFile($this->contents);
		}

		return $this->contents;
	}

	/**
	 * Getter for path to bundle
	 *
	 * @return  string
	 */
	protected function getWrapperPath()
	{
		$path = $this->publication()->bundlePath();

		return $path;
	}

	/**
	 * Replace bundle element w/ hierarchical array of contents
	 *
	 * @param   array   $contents
	 * @return  void
	 */
	protected function expandBundleFile(&$contents)
	{
		$bundlePath = $this->getBundlePath();
		$bundleIndex = $this->getBundleIndex($contents);

		$bundleContents = [];
		$bundleContents['isDirectory'] = true;
		$bundleContents['name'] = 'bundle.zip';
		$bundleContents['contents'] = ZipHelper::getArchiveContents($bundlePath);

		unset($contents[$bundleIndex]);
		if ($bundleContents['contents'])
		{
			$contents['bundle'] = $bundleContents;
		}
	}

	/**
	 * Gets index of bundle element
	 *
	 * @param   array     $contents
	 * @return  integer
	 */
	protected static function getBundleIndex($contents)
	{
		$bundleIndex = null;

		foreach ($contents as $index => $entry)
		{
			if (isset($entry['name']) && $entry['name'] === 'bundle.zip')
			{
				$bundleIndex = $index;
				break;
			}
		}

		return $bundleIndex;
	}

	/**
	 * Builds path to bundle.zip archive
	 *
	 * @return  string
	 */
	protected function getBundlePath()
	{
		$wrapperPath = $this->getWrapperPath();
		$pathSegments = explode('/', $wrapperPath);
		array_pop($pathSegments);

		$bundlePath = implode('/', $pathSegments) . '/bundles/bundle.zip';

		return $bundlePath;
	}

	/**
	 * Getter for associated publication
	 *
	 * @return  object  \Publication
	 */
	public function publication()
	{
		if (!isset($this->publication))
		{
			$this->publication = new Publication($this->publication_id);
		}

		return $this->publication;
	}
}
