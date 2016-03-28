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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once __DIR__ . DS . 'models' . DS . 'metadata.php';

/**
 * Plugin class for fez metadata handling
 */
class plgMetadataLocal extends \Hubzero\Plugin\Plugin
{
	/**
	 * Responds to events for saving file metadata
	 *
	 * @param   Hubzero\Filesystem\File  $file      The file to which the metadata pertains
	 * @param   array                    $metadata  The metadata itself
	 * @return  void
	 **/
	public function onMetadataSave(Hubzero\Filesystem\File $file, $metadata)
	{
		foreach ($metadata as $key => $value)
		{
			if (!$file->isLocal())
			{
				return false;
			}

			$metadata = Metadata::oneOrNewByPathAndKey($file->getAbsolutePath(), $key);

			$metadata->set('value', (string) $value)->save();
		}
	}

	/**
	 * Responds to events for getting the latest metadata annotation set
	 *
	 * @param   Hubzero\Filesystem\File  $file        The file to which the metadata pertains
	 * @param   int                      $maxEntries  The maximum number of entries to return
	 * @return  array
	 **/
	public function onMetadataGet(Hubzero\Filesystem\File $file, $maxEntries = 1)
	{
		if (!$file->isLocal())
		{
			return false;
		}

		$metadata = Metadata::loadAllByPath($file->getAbsolutePath());
		$results  = [];

		foreach ($metadata as $data)
		{
			$results[$data->key] = $data->value;
		}

		return $results;
	}

	/**
	 * Responds to file move events so that we can rename applicable metadata paths
	 *
	 * @param   string  $old  The starting path name
	 * @param   string  $new  The ending path name
	 * @return  bool
	 **/
	public function onFileMove($old, $new)
	{
		return Metadata::relocateByPath($old, $new);
	}
}