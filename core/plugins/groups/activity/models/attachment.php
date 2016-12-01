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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Activity\Models;

use Hubzero\Base\Object;
use Hubzero\Filesystem\Util;
use Hubzero\Utility\Number;
use Filesystem;
use Lang;

/**
 * Class for comment files (attachments)
 */
class Attachment extends Object
{
	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $size = null;

	/**
	 * Dimensions for file (must be an image)
	 *
	 * @var  array
	 */
	protected $dimensions = null;

	/**
	 * Upload directory (relative to PATH_APP)
	 *
	 * @var  string
	 */
	protected $uploadDir = null;

	/**
	 * Set the upload path
	 *
	 * @param   string  $path  Path to set to
	 * @return  object
	 */
	public function setUploadDir($path)
	{
		$path = str_replace(' ', '_', trim($path));
		$path = Util::normalizePath($path);

		if (substr($path, 0, strlen(PATH_APP)) == PATH_APP)
		{
			$path = substr($path, strlen(PATH_APP));
		}

		$this->uploadDir = ($path ? $path : $this->uploadDir);

		return $this;
	}

	/**
	 * Get the upload path
	 *
	 * @return  string
	 */
	public function getUploadDir()
	{
		return PATH_APP . $this->uploadDir;
	}

	/**
	 * Ensure no invalid characters
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticFilename($data)
	{
		$filename = $data['filename'];
		$filename = preg_replace("/[^A-Za-z0-9.]/i", '-', $filename);

		$ext = strrchr($filename, '.');
		$prefix = substr($filename, 0, -strlen($ext));

		if (strlen($prefix) > 240)
		{
			$prefix = substr($prefix, 0, 240);
			$filename = $prefix . $ext;
		}

		$data['filename'] = $filename;

		return $data['filename'];
	}

	/**
	 * Ensure no conflicting file names by
	 * renaming the incoming file if the name
	 * already exists
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function uniqueFilename($data)
	{
		$filename = $this->automaticFilename($data);

		if (file_exists($this->getUploadDir() . ($this->get('subdir') ? DS . $this->get('subdir') : '') . DS . $filename))
		{
			$ext = strrchr($filename, '.');
			$prefix = substr($filename, 0, -strlen($ext));

			$i = 1;

			while (is_file($this->getUploadDir() . ($this->get('subdir') ? DS . $this->get('subdir') : '') . DS . $filename))
			{
				$filename = $prefix . ++$i . $ext;
			}
		}

		$data['filename'] = $filename;

		return $data['filename'];
	}

	/**
	 * Delete record
	 *
	 * @return  boolean  True if successful, False if not
	 */
	public function destroy()
	{
		$path = $this->path();

		if (file_exists($path))
		{
			if (!Filesystem::delete($path))
			{
				$this->setError('Unable to delete file.');

				return false;
			}
		}

		return true;
	}

	/**
	 * Upload file
	 *
	 * @param   string  $name
	 * @param   string  $temp
	 * @return  bool
	 */
	public function upload($name, $temp, $size)
	{
		$destination = $this->getUploadDir() . ($this->get('subdir') ? DS . $this->get('subdir') : '');

		// Make sure destination directory exists
		if (!is_dir($destination))
		{
			if (!Filesystem::makeDirectory($destination))
			{
				$this->setError('COM_GROUPS_MEDIA_UNABLE_TO_CREATE_UPLOAD_PATH');
				return false;
			}
		}
		if (!is_writable($destination))
		{
			$this->setError(Lang::txt('COM_GROUPS_MEDIA_PATH_NOT_WRITABLE'));
			return false;
		}

		$config = \Component::params('com_media');

		// Check for allowed file types
		$ext = Filesystem::extension($name);

		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));
		if ($allowedExtensions && !in_array($ext, $allowedExtensions))
		{
			$this->setError(Lang::txt('COM_GROUPS_MEDIA_INVALID_FILE', implode(', ', $allowedExtensions)));
			return false;
		}

		// Max upload size
		$sizeLimit = $config->get('upload_maxsize');
		$sizeLimit = $sizeLimit * 1024 * 1024;

		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			$this->setError(Lang::txt('COM_GROUPS_MEDIA_FILE_TOO_BIG', $max));
			return false;
		}

		// Make sure there are no filename conflicts
		$filename = $this->uniqueFilename(array(
			'filename' => $name,
			'subdir'   => $this->get('subdir')
		));

		$destination .= DS . $filename;

		if (!Filesystem::upload($temp, $destination))
		{
			$this->setError('COM_GROUPS_MEDIA_ERROR_UPLOADING');
			return false;
		}

		// Change file perm
		chmod($destination, 0774);

		// Scan file for viruses and other nasty bits
		if (!Filesystem::isSafe($destination))
		{
			// Delete file
			unlink($destination);

			$this->setError(Lang::txt('COM_GROUPS_MEDIA_FILE_CONTAINS_VIRUS'));
			return false;
		}

		$this->set('filename', $filename);

		return true;
	}

	/**
	 * File path
	 *
	 * @return  integer
	 */
	public function path()
	{
		return $this->getUploadDir() . DS . $this->get('filename');
	}

	/**
	 * If file exists at the given path
	 *
	 * @return  bool
	 */
	public function exists()
	{
		return file_exists($this->path());
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function size()
	{
		if ($this->size === null)
		{
			$this->size = 0;

			$path = $this->path();

			if (file_exists($path))
			{
				$this->size = filesize($path);
			}
		}

		return $this->size;
	}

	/**
	 * File width and height
	 *
	 * @return  array
	 */
	public function dimensions()
	{
		if (!$this->dimensions)
		{
			$this->dimensions = array(0, 0);

			if ($this->isImage() && file_exists($this->path()))
			{
				$this->dimensions = getimagesize($this->path());
			}
		}

		return $this->dimensions;
	}

	/**
	 * File width
	 *
	 * @return  integer
	 */
	public function width()
	{
		$dimensions = $this->dimensions();

		return $dimensions[0];
	}

	/**
	 * File height
	 *
	 * @return  integer
	 */
	public function height()
	{
		$dimensions = $this->dimensions();

		return $dimensions[1];
	}

	/**
	 * File height
	 *
	 * @return  array
	 */
	public function toArray()
	{
		return array(
			'filename'    => $this->get('filename'),
			'path'        => $this->uploadDir,
			'description' => $this->get('description'),
			'subdir'      => $this->get('subdir')
		);
	}
}
