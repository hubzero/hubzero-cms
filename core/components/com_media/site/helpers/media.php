<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Site\Helpers;

/**
 * Media helper
 */
class Media
{
	/**
	 * Checks if the file is an image
	 *
	 * @param   string   $fileName  The filename
	 * @return  boolean
	 */
	public static function isImage($fileName)
	{
		static $imageTypes = 'xcf|odg|gif|jpg|png|bmp';

		return preg_match("/$imageTypes/i", $fileName);
	}

	/**
	 * Checks if the file is an image
	 *
	 * @param   string   $fileName  The filename
	 * @return  boolean
	 */
	public static function getTypeIcon($fileName)
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param   array    $file  File information
	 * @param   string   $err   An error message to be returned
	 * @return  boolean
	 */
	public static function canUpload($file, &$err)
	{
		$params = Component::params('com_media');

		$format = Filesystem::extension($file['name']);

		$allowable = explode(',', $params->get('upload_extensions'));

		if (!in_array($format, $allowable))
		{
			$err = Lang::txt('COM_MEDIA_ERROR_WARNFILETYPE');
			return false;
		}

		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$err = Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE');

			return false;
		}

		return true;
	}

	/**
	 * Return file size in human readable format
	 *
	 * @param   integer  $size
	 * @return  string
	 */
	public static function parseSize($size)
	{
		if ($size < 1024)
		{
			return Lang::txt('COM_MEDIA_FILESIZE_BYTES', $size);
		}
		elseif ($size < 1024 * 1024)
		{
			return Lang::txt('COM_MEDIA_FILESIZE_KILOBYTES', sprintf('%01.2f', $size / 1024.0));
		}
		else
		{
			return Lang::txt('COM_MEDIA_FILESIZE_MEGABYTES', sprintf('%01.2f', $size / (1024.0 * 1024)));
		}
	}

	/**
	 * Resize an image
	 *
	 * @param   integer  $width
	 * @param   integer  $height
	 * @param   integer  $target
	 * @return  string
	 */
	public static function imageResize($width, $height, $target)
	{
		// takes the larger size of the width and height and applies the
		// formula accordingly...this is so this script will work
		// dynamically with any size image
		if ($width > $height)
		{
			$percentage = ($target / $width);
		}
		else
		{
			$percentage = ($target / $height);
		}

		// gets the new value and applies the percentage, then rounds the value
		$width  = round($width * $percentage);
		$height = round($height * $percentage);

		// returns the new sizes in html image tag format...this is so you
		// can plug this function inside an image tag and just get the
		return "width=\"$width\" height=\"$height\"";
	}

	/**
	 * Count files in a directory
	 *
	 * @param   string   $dir
	 * @return  array
	 */
	public static function countFiles($dir)
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir))
		{
			$d = dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry, 0, 1) != '.'
				 && is_file($dir . DIRECTORY_SEPARATOR . $entry)
				 && strpos($entry, '.html') === false
				 && strpos($entry, '.php') === false)
				{
					$total_file++;
				}

				if (substr($entry, 0, 1) != '.'
				 && is_dir($dir . DIRECTORY_SEPARATOR . $entry))
				{
					$total_dir++;
				}
			}

			$d->close();
		}

		return array($total_file, $total_dir);
	}
}
