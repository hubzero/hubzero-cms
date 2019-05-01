<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Helpers;

use Filesystem;
use Component;
use Notify;
use User;
use Lang;

/**
 * Media helper
 */
class MediaHelper
{
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

		if (empty($file['name']))
		{
			$err = 'COM_MEDIA_ERROR_UPLOAD_INPUT';
			return false;
		}

		if ($file['name'] !== Filesystem::clean($file['name']))
		{
			$err = 'COM_MEDIA_ERROR_WARNFILENAME';
			return false;
		}

		$format = strtolower(Filesystem::extension($file['name']));

		// Media file names should never have executable extensions buried in them.
		$executable = array(
			'php', 'js', 'exe', 'phtml', 'java', 'perl', 'py', 'asp','dll', 'go', 'ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'hta', 'ins', 'isp',
			'jse', 'lib', 'mde', 'msc', 'msp', 'mst', 'pif', 'scr', 'sct', 'shb', 'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh'
		);

		$explodedFileName = explode('.', $file['name']);

		if (count($explodedFileName) > 2)
		{
			foreach ($executable as $extensionName)
			{
				if (in_array($extensionName, $explodedFileName))
				{
					$err = 'COM_MEDIA_ERROR_WARNFILETYPE';
					return false;
				}
			}
		}

		$allowable = explode(',', $params->get('upload_extensions'));
		$ignored   = explode(',', $params->get('ignore_extensions'));

		if ($format == '' || $format == false || (!in_array($format, $allowable) && !in_array($format, $ignored)))
		{
			$err = 'COM_MEDIA_ERROR_WARNFILETYPE';
			return false;
		}

		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$err = 'COM_MEDIA_ERROR_WARNFILETOOLARGE';
			return false;
		}

		$imginfo = null;
		if ($params->get('restrict_uploads', 1))
		{
			$images = explode(',', $params->get('image_extensions'));

			// if it's an image run it through getimagesize
			if (in_array($format, $images))
			{
				// if tmp_name is empty, then the file was bigger than the PHP limit
				if (!empty($file['tmp_name']))
				{
					if (($imginfo = getimagesize($file['tmp_name'])) === false)
					{
						$err = 'COM_MEDIA_ERROR_WARNINVALID_IMG';
						return false;
					}
				}
				else
				{
					$err = 'COM_MEDIA_ERROR_WARNFILETOOLARGE';
					return false;
				}
			}
			elseif (!in_array($format, $ignored))
			{
				// if its not an image...and we're not ignoring it
				$allowed_mime = explode(',', $params->get('upload_mime'));
				$illegal_mime = explode(',', $params->get('upload_mime_illegal'));

				if (function_exists('finfo_open') && $params->get('check_mime', 1))
				{
					// We have fileinfo
					$finfo = finfo_open(FILEINFO_MIME);
					$type = finfo_file($finfo, $file['tmp_name']);
					if (strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime))
					{
						$err = 'COM_MEDIA_ERROR_WARNINVALID_MIME';
						return false;
					}

					finfo_close($finfo);
				}
				elseif (function_exists('mime_content_type') && $params->get('check_mime', 1))
				{
					// we have mime magic
					$type = mime_content_type($file['tmp_name']);

					if (strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime))
					{
						$err = 'COM_MEDIA_ERROR_WARNINVALID_MIME';
						return false;
					}
				}
				elseif (!User::authorise('core.manage'))
				{
					$err = 'COM_MEDIA_ERROR_WARNNOTADMIN';
					return false;
				}
			}
		}

		if (!User::authorise('core.admin'))
		{
			$xss_check = Filesystem::read($file['tmp_name'], false, 256);

			$html_tags = array(
				'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound',
				'big', 'blackface', 'blink', 'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite',
				'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em',
				'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
				'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label',
				'layer', 'legend', 'li', 'limittext', 'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol',
				'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object', 'ol', 'optgroup', 'option',
				'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow',
				'sidebar', 'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td',
				'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--'
			);

			foreach ($html_tags as $tag)
			{
				// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
				if (stristr($xss_check, '<' . $tag . ' ')
				 || stristr($xss_check, '<' . $tag . '>'))
				{
					$err = 'COM_MEDIA_ERROR_WARNIEXSS';
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Returns a quantifier based on the argument
	 *
	 * @param   integer  $size  Numeric size of a file
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
	 * Find new sizes for an image
	 *
	 * @param   integer  $width   Original width
	 * @param   integer  $height  Original height
	 * @param   integer  $target  Target size
	 * @return  array
	 */
	public static function imageResize($width, $height, $target)
	{
		// Take the larger size of the width and height and applies the
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

		// Get the new value, apply the percentage, and round the value
		$width  = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	/**
	 * Count files in a directory
	 *
	 * @param   string  $dir  Directory
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
				 && strpos($entry, '.html') === false && strpos($entry, '.php') === false)
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

	/**
	 * Get parent directory
	 *
	 * @param   string  $folder
	 * @return  string
	 */
	public static function getParent($folder)
	{
		$parent = substr($folder, 0, strrpos($folder, '/'));
		return $parent;
	}

	/**
	 * Get children
	 *
	 * @param   string  $directory
	 * @param   string  $folder
	 * @return  array
	 */
	public static function getChildren($directory, $folder)
	{
		$children = Filesystem::listContents($directory . $folder);

		foreach ($children as &$child)
		{
			$child['name'] = str_replace('/', '', substr($child['path'], 0, strlen($child['path'])));
			$child['path'] = $folder . $child['path'];

			if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $child['name']))
			{
				$child['type'] = 'img';
			}
		}

		return $children;
	}

	/**
	 * Build a folder tree
	 *
	 * @param   array   $folders
	 * @param   string  $path
	 * @return  void
	 */
	public static function _buildFolderTree($folders, $parent_id = 0, $path = '')
	{
		$branch = array();
		foreach ($folders as $folder)
		{
			if ($folder['parent'] == $parent_id)
			{
				$folder['path'] = ($path == '') ? $folder['name'] : $path . '/' . $folder['name'];

				$children = self::_buildFolderTree($folders, $folder['id'], $folder['path']);
				if ($children)
				{
					$folder['children'] = $children;
				}

				$branch[] = $folder;
			}
		}
		return $branch;
	}

	/**
	 * Build a path
	 *
	 * @param   array   $folders
	 * @param   string  $path
	 * @return  void
	 */
	public static function createPath(&$folders, $path)
	{
		foreach ($folders as &$folder)
		{
			$folder['path'] = str_replace($path, '', $folder['fullname']);
		}
	}
}
