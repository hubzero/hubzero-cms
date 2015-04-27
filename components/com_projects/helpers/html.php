<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Helpers;

use Exception;
use Hubzero\Base\Object;

/**
 * Html helper class
 */
class Html extends Object
{
	/**
	 * Show time since present moment or an actual date
	 *
	 * @param      string 	$time
	 * @param      boolean 	$utc	UTC
	 * @return     string
	 */
	public static function showTime($time, $utc = false)
	{
		$parsed 		= date_parse($time);
		$timestamp		= strtotime($time);
		$current_time 	= $utc ? \JFactory::getDate() : date('c');
		$current  		= date_parse($current_time);
		$lapsed 		= strtotime($current_time) - $timestamp;

		if ($lapsed < 30)
		{
			return Lang::txt('just now');
		}
		elseif ($lapsed > 86400 && $current['year'] != $parsed['year'])
		{
			return \JHTML::_('date', $timestamp, 'M j, Y', false);
		}
		elseif ($lapsed > 86400)
		{
			return \JHTML::_('date', $timestamp, 'M j', false) . ' at ' . \JHTML::_('date', $timestamp, 'h:ia', false);
		}
		else
		{
			return self::timeDifference($lapsed);
		}
	}

	/**
	 * Specially formatted time display
	 *
	 * @param      string 	$time
	 * @param      boolean 	$full	Return detailed date/time?
	 * @param      boolean 	$utc	UTC
	 * @return     string
	 */
	public static function formatTime($time, $full = false, $utc = false)
	{
		$parsed 	= date_parse($time);
		$timestamp	= strtotime($time);

		$now 		= $utc ? Date::toSql() : date('c');
		$current  	= date_parse($now);

		if ($full)
		{
			return \JHTML::_('date', $timestamp, 'M d, Y H:i:s', false);
		}

		if ($current['year'] == $parsed['year'])
		{
			if ($current['month'] == $parsed['month'] && $current['day'] == $parsed['day'])
			{
				return \JHTML::_('date', $timestamp, 'g:i A', false);
			}
			else
			{
				return \JHTML::_('date', $timestamp, 'M j', false);
			}
		}
		else
		{
			return \JHTML::_('date', $timestamp, 'M j, Y', false);
		}
	}

	/**
	 * Time elapsed from moment
	 *
	 * @param      string 	$timestamp
	 * @param      boolean 	$utc	UTC
	 * @return     string
	 */
	public static function timeAgo($timestamp, $utc = true)
	{
		$timestamp = strtotime($timestamp);

		// Get current time
		$current_time = $utc ? strtotime(\JFactory::getDate()) : strtotime(date('c'));

		$text = self::timeDifference($current_time - $timestamp);

		return $text;
	}

	/**
	 * Get time difference
	 *
	 * @param      string $difference
	 * @return     string
	 */
	public static function timeDifference ($difference)
	{
		// Set the periods of time
		$periods = array('sec', 'min', 'hr', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0)
		{
			$val = 0;
		}

		// Set the current value to be floored
		$number = floor($number);
		if ($periods[$val] == 'sec')
		{
			return Lang::txt('COM_PROJECTS_LESS_THAN_A_MINUTE');
		}

		// If required create a plural
		if ($number != 1)
		{
			$periods[$val] .= 's';
		}

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];
		if ($text == '0 seconds')
		{
			$text = Lang::txt('COM_PROJECTS_JUST_A_MOMENT');
		}

		return $text;
	}

	/**
	 * Get file extension
	 *
	 * @param      string $file
	 * @return     string
	 */
	public static function getFileExtension( $file = '')
	{
		if (!is_null($file))
		{
			$dot = strrpos($file, '.') + 1;

			return strtolower(substr($file, $dot));
		}
		return NULL;
	}

	/**
	 * Get directory size
	 *
	 * @param      string $directory
	 * @return     string
	 */
	public static function getDirSize ($directory = '')
	{
		if (!$directory)
		{
			return 0;
		}
		$fileSystem = new \Hubzero\Filesystem\Filesystem();

		return $fileSystem->size($directory);
	}

	/**
	 * Convert file size
	 *
	 * @param      int $file_size
	 * @param      string $from
	 * @param      string $to
	 * @param      string $round
	 * @return     string
	 */
	public static function convertSize($file_size, $from = 'b', $to = 'GB', $round = 0)
	{
		$file_size = str_replace(' ', '', $file_size);

		if ($from == 'b')
		{
			if ($to == 'GB')
			{
				$file_size = round(($file_size / 1073741824 * 100), $round) / 100;
			}
			elseif ($to == 'MB')
			{
				$file_size = round(($file_size / 1048576 * 100), $round) / 100 ;
			}
			elseif ($to == 'KB')
			{
				$file_size = round(($file_size / 1024 * 100) / 100, $round);
			}
		}
		elseif ($from == 'GB')
		{
			if ($to == 'b')
			{
				$file_size = $file_size * 1073741824;
			}
			if ($to == 'KB')
			{
				$file_size = $file_size * 1048576;
			}
			if ($to == 'MB')
			{
				$file_size = $file_size * 1024;
			}
		}

		return $file_size;
	}

	/**
	 * Get google icon image
	 *
	 * @param      string $mimeType
	 * @param      boolean $include_dir
	 * @param      string $icon
	 * @return     string
	 */
	public static function getGoogleIcon ($mimeType, $include_dir = 1, $icon = '')
	{
		switch (strtolower($mimeType))
		{
			case 'application/vnd.google-apps.presentation':
				$icon = 'presentation';
				break;

			case 'application/vnd.google-apps.spreadsheet':
				$icon = 'sheet';
				break;

			case 'application/vnd.google-apps.document':
				$icon = 'doc';
				break;

			case 'application/vnd.google-apps.drawing':
				$icon = 'drawing';
				break;

			case 'application/vnd.google-apps.form':
				$icon = 'form';
				break;

			case 'application/vnd.google-apps.folder':
				$icon = 'folder';
				break;

			default:
				$icon = 'gdrive';
				break;
		}

		if ($include_dir)
		{
			$icon = "/plugins/projects/files/images/google/" . $icon . '.gif';
		}
		return $icon;

	}

	/**
	 * Fix up some mimetypes
	 *
	 * @param      string $file
	 * @param      string $mimeType
	 * @return     string
	 */
	public static function fixUpMimeType ($file = NULL, $mimeType = NULL)
	{
		if ($file)
		{
			// Get file extention
			$parts = explode('.', $file);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';
			$ext   = strtolower($ext);

			switch (strtolower($ext))
			{
				case 'key':
					$mimeType = 'application/x-iwork-keynote-sffkey';
					break;

				case 'ods':
					$mimeType = 'application/vnd.oasis.opendocument.spreadsheet';
					break;

				case 'wmf':
					$mimeType = 'application/x-msmetafile';
					break;

				case 'tex':
					$mimeType = 'application/x-tex';
					break;
			}
		}

		return $mimeType;
	}

	/**
	 * Get name for a number
	 *
	 * @param      integer $int
	 * @return     string
	 */
	public static function getNumberName($int = 0)
	{
		$name = '';

		switch ($int)
		{
			case 1:
				$name = 'one';
			break;

			case 2:
				$name = 'two';
			break;

			case 3:
				$name = 'three';
			break;

			case 4:
				$name = 'four';
			break;

			case 5:
				$name = 'five';
			break;

			case 6:
				$name = 'six';
			break;

			case 7:
				$name = 'seven';
			break;

			case 8:
				$name = 'eight';
			break;

			case 9:
				$name = 'nine';
			break;

			case 10:
				$name = 'ten';
			break;
		}

		return $name;
	}

	/**
	 * Get file icon image
	 *
	 * @param      string $ext
	 * @param      boolean $include_dir
	 * @param      string $icon
	 * @return     string
	 */
	public static function getFileIcon ($ext, $include_dir = 1, $icon = '')
	{
		switch (strtolower($ext))
		{
			case 'pdf':
				$icon = 'page_white_acrobat';
				break;
			case 'txt':
			case 'css':
			case 'rtf':
			case 'sty':
			case 'cls':
			case 'log':
				$icon = 'page_white_text';
				break;
			case 'sql':
				$icon = 'page_white_sql';
				break;
			case 'm':
				$icon = 'page_white_matlab';
				break;
			case 'dmg':
			case 'exe':
			case 'va':
			case 'ini':
				$icon = 'page_white_gear';
				break;
			case 'eps':
			case 'ai':
			case 'wmf':
				$icon = 'page_white_vector';
				break;
			case 'php':
				$icon = 'page_white_php';
				break;
			case 'tex':
			case 'ltx':
				$icon = 'page_white_tex';
				break;
			case 'swf':
				$icon = 'page_white_flash';
				break;
			case 'key':
				$icon = 'page_white_keynote';
				break;
			case 'numbers':
				$icon = 'page_white_numbers';
				break;
			case 'pages':
				$icon = 'page_white_pages';
				break;
			case 'html':
			case 'htm':
				$icon = 'page_white_code';
				break;
			case 'xls':
			case 'xlsx':
			case 'tsv':
			case 'csv':
			case 'ods':
				$icon = 'page_white_excel';
				break;
			case 'ppt':
			case 'pptx':
			case 'pps':
				$icon = 'page_white_powerpoint';
				break;
			case 'mov':
			case 'mp4':
			case 'm4v':
			case 'avi':
				$icon = 'page_white_film';
				break;
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'tiff':
			case 'bmp':
			case 'png':
				$icon = 'page_white_picture';
				break;
			case 'mp3':
			case 'aiff':
			case 'm4a':
			case 'wav':
				$icon = 'page_white_sound';
				break;
			case 'zip':
			case 'rar':
			case 'gz':
			case 'sit':
			case 'sitx':
			case 'zipx':
			case 'tar':
			case '7z':
				$icon = 'page_white_compressed';
				break;
			case 'doc':
			case 'docx':
				$icon = 'page_white_word';
				break;
			case 'folder':
				$icon = 'folder';
				break;
			default:
				$icon = 'page_white';
				break;
		}

		if ($include_dir)
		{
			$icon = "/plugins/projects/files/images/" . $icon . '.gif';
		}
		return $icon;
	}

	/**
	 * Get array of available emotion icons
	 *
	 * @return     array
	 */
	public static function getEmoIcons()
	{
		$icons = array(
				':)'    =>  'happy',
				':-)'   =>  'grin',
				':D'    =>  'laugh',
				':d'    =>  'laugh',
				';)'    =>  'wink',
				':P'    =>  'tongue',
				':-P'   =>  'tongue',
				':-p'   =>  'tongue',
				':p'    =>  'tongue',
				':('    =>  'unhappy',
				':\'('	=>	'cry',
				':o'    =>  'surprised',
				':O'    =>  'surprised',
				':0'    =>  'surprised',
				':|'    =>  'displeased',
				':-|'   =>  'displeased',
				':/'    =>  'displeased',
				'8|'    =>  'sunglasses',
				'O:)'   =>  'saint',
				'>:O'   =>  'angry',
				':-/'   =>  'surprised',
				'l-)'   =>  'sleep',
				'(y)'   =>  'thumbsup',
				'^_^'   =>  'squint',
				'-_-'   =>  'squint',
				'3:)'   =>  'devil'
		);

		return $icons;
	}

	/**
	 * Check if file is binary
	 *
	 * @param      string	$file
	 *
	 * @return     integer
	 */
	public static function isBinary($file)
	{
		// MIME types
		$mt = new \Hubzero\Content\Mimetypes();
		$mime = $mt->getMimeType( $file );

		return substr($mime, 0, 4) == 'text' ? false : true;
	}

	/**
	 * Replace with emotion icons
	 *
	 * @param      string $text
	 * @return     string
	 */
	public static function replaceEmoIcons($text = NULL)
	{
		$icons = self::getEmoIcons();

		foreach ($icons as $icon => $image)
		{
			$pat 	=  '#(?<=\s|^)(' . preg_quote($icon) .')(?=\s|$)#';
			$rep  	= '<span class="icon-emo-' . $image . '"></span>';
			$text 	= preg_replace($pat, $rep, $text);
		}

		return $text;
	}

	/**
	 * Get project image source
	 *
	 * @param      string $alias
	 * @param      string $picture
	 * @param      array $config
	 * @return     string
	 */
	public static function getProjectImageSrc( $alias = '', $picture = '', $config = '' )
	{
		if ($alias === NULL)
		{
			return false;
		}
		if (!$config)
		{
			$config = Component::params('com_projects');
		}
		$path    = trim($config->get('imagepath', '/site/projects'), DS) . DS . $alias . DS . 'images';
		$default = trim($config->get('masterpic', '/components/com_projects/site/assets/img/projects-large.gif'), DS);

		$default = is_file( PATH_APP . DS . $default ) ? $default : NULL;

		$src  = $picture && is_file( PATH_APP . DS . $path . DS . $picture )
				? $path . DS . $picture
				: $default;
		return $src;
	}

	/**
	 * Get project thumbnail source
	 *
	 * @param      string $alias
	 * @param      string $picname
	 * @param      array $config
	 * @return     string
	 */
	public static function getThumbSrc( $alias = '', $picture = '', $config = '' )
	{
		if ($alias === NULL)
		{
			return false;
		}
		if (!$config)
		{
			$config = Component::params('com_projects');
		}

		$src  = '';
		$path = DS . trim($config->get('imagepath', '/site/projects'), DS) . DS . $alias . DS . 'images';

		if (file_exists( PATH_APP . $path . DS . 'thumb.png' ))
		{
			return $path . DS . 'thumb.png';
		}

		if ($picture)
		{
			$thumb = self::createThumbName($picture);
			$src = $thumb && file_exists( PATH_APP . $path . DS . $thumb ) ? $path . DS . $thumb :  NULL;
		}
		if (!$src)
		{
			$src = $config->get('defaultpic');
		}

		return $src;
	}

	/**
	 * Create a thumbnail name
	 *
	 * @param      string $image Image name
	 * @param      string $tn    Thumbnail prefix
	 * @param      string $ext
	 * @return     string
	 */
	public static function createThumbName( $image = null, $tn = '_thumb', $ext = 'png' )
	{
		return \JFile::stripExt($image) . $tn . '.' . $ext;
	}

	/**
	 * Generate random code
	 *
	 * @param      int $minlength
	 * @param      int $maxlength
	 * @param      boolean $usespecial
	 * @param      boolean $usenumbers
	 * @param      boolean $useletters
	 * @return     string HTML
	 */
	public static function generateCode(
		$minlength = 10, $maxlength = 10, $usespecial = 0,
		$usenumbers = 0, $useletters = 1, $mixedcaps = false
	)
	{
		$key = '';
		$charset = '';
		if ($useletters)
		{
			$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		if ($useletters && $mixedcaps)
		{
			$charset .= "abcdefghijklmnopqrstuvwxyz";
		}
		if ($usenumbers)
		{
			$charset .= "0123456789";
		}
		if ($usespecial)
		{
			$charset .= "~@#$%^*()_+-={}|][";
		}
		if ($minlength > $maxlength)
		{
			$length = mt_rand ($maxlength, $minlength);
		}
		else
		{
			$length = mt_rand ($minlength, $maxlength);
		}
		for ($i=0; $i<$length; $i++)
		{
			$key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		}
		return $key;
	}

	/**
	 * Replace urls in text
	 *
	 * @param      string $string
	 * @param      string $rel
	 * @return     string HTML
	 */
	public static function replaceUrls($string, $rel = 'nofollow')
	{
	    return preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', "<a href=\"$1\" rel=\"{$rel}\">$1</a>", $string);
	}

	/**
	 * Search for value in array
	 *
	 * @param      string $needle
	 * @param      string $haystack
	 * @return     boolean
	 */
	public static function myArraySearch( $needle, $haystack )
	{
	    if (empty($needle) || empty($haystack))
		{
			return false;
		}

		foreach ($haystack as $key => $value)
		{
			$exists = 0;
			foreach ($needle as $nkey => $nvalue)
			{
				if (!empty($value->$nkey) && $value->$nkey == $nvalue)
				{
					$exists = 1;
				}
				else
				{
					$exists = 0;
				}
			}
			if ($exists)
			{
				return $key;
			}
		}

		return false;
	}

	/**
	 * Covert param to array of values
	 *
	 * @param      string $param
	 *
	 * @return     array
	 */
	public static function getParamArray($param = '')
	{
		if ($param)
		{
			$array = explode(',', $param);
			return array_map('trim', $array);
		}
		return array();
	}

	/**
	 * Covert param to array of values
	 *
	 * @param      string $param
	 *
	 * @return     array
	 */
	public static function getCountArray($array = array())
	{
		$counts = array();
		if (!empty($array))
		{
			foreach ($array as $a)
			{
				if (!empty($a))
				{
					foreach ($a as $key => $value)
					{
						$counts[$key] = $value;
					}
				}
			}
		}
		return $counts;
	}

	/**
	 * Get the random number appended to file name
	 *
	 * @param      string $path
	 *
	 * @return     string
	 */
	public static function getAppendedNumber ( $path = null )
	{
		$append = '';

		$dirname 	= dirname($path);
		$filename 	= basename($path);
		$name 		= '';
		$file = explode('.', $filename);

		$n = count($file);
		if ($n > 1)
		{
			$name = $file[$n-2];
		}
		else
		{
			$name = $path;
		}

		$parts = explode('-', $name);
		if (count($parts) > 1)
		{
			$append = intval(end($parts));
		}

		return $append;
	}

	/**
	 * Replace file ending
	 *
	 * @param      string $path
	 * @param      string $end
	 * @param      string $delim
	 * @return     string
	 */
	public static function cleanFileNum ( $path = null, $end = '', $delim = '-' )
	{
		$newpath = $path;

		if ($end)
		{
			$file = explode('.', $path);
			$n = count($file);
			$ext = '';
			if ($n > 1)
			{
				$name = $file[$n-2];
				$ext  = array_pop($file);
			}
			else
			{
				$name = $path;
			}

			$parts = explode($delim, $name);
			if (count($parts) > 1)
			{
				$oldnum = intval(end($parts));
				if ($oldnum == $end)
				{
					$out = array_pop($parts);
					$name = implode('', $parts);
				}
			}

			$newpath = $ext ? $name . '.' . $ext : $name;
		}

		return $newpath;
	}

	/**
	 * Append string to file name
	 *
	 * @param      string $path
	 * @param      string $append
	 * @param      string $ext
	 * @return     string
	 */
	public static function fixFileName ( $path = null, $append = '', $ext = '' )
	{
		if (!$path)
		{
			return false;
		}

		if (!$append)
		{
			return $path;
		}

		$newname 	= '';
		$dirname 	= dirname($path);
		$filename 	= basename($path);

		$file = explode('.', $filename);
		$n = count($file);
		if ($n > 1)
		{
			$file[$n-2] .= $append;

			$end = array_pop($file);
			$file[] = $end;
			$filename = implode('.',$file);
		}
		else
		{
			$filename = $filename . $append;
		}

		if ($ext)
		{
			$filename = $filename . '.' . $ext;
		}

		$newname = $dirname && $dirname != '.' ? $dirname . DS . $filename : $filename;

		return $newname;
	}

	/**
	 * Return filename without extension
	 *
	 * @param      string  $filename      Filename string to shorten
	 * @return     string
	 */
	public static function takeOutExt($filename = '')
	{
		// Take out extention
		if ($filename)
		{
			$parts = explode('.', $filename);

			if (count($parts) > 1)
			{
				$end = array_pop($parts);
			}

			if (count($parts) > 1)
			{
				$end = array_pop($parts);
			}

			$filename = implode($parts);
		}

		return $filename;
	}

	/**
	 * Shorten a string to a max length, preserving whole words
	 *
	 * @param      string  $text      String to shorten
	 * @param      integer $chars     Max length to allow
	 * @return     string
	 */
	public static function shortenText($text, $chars=300)
	{
		$text = trim($text);

		if (strlen($text) > $chars)
		{
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
		}

		return $text;
	}

	/**
	 * Shorten user full name
	 *
	 * @param      string $name
	 * @param      int $chars
	 * @return     string
	 */
	public static function shortenName( $name, $chars = 12 )
	{
		$name = trim($name);

		if (strlen($name) > $chars)
		{
			$names = explode(' ',$name);
			$name = $names[0];
			if (count($names) > 0 && $names[1] != '')
			{
				$name  = $name.' ';
				$name .= substr($names[1], 0, 1);
				$name .= '.';
			}
		}
		if ($name == '')
		{
			$name = Lang::txt('COM_PROJECTS_UNKNOWN');
		}

		return $name;
	}

	/**
	 * Shorten file name
	 *
	 * @param      string $name
	 * @param      int $chars
	 * @return     string
	 */
	public static function shortenFileName( $name, $chars = 30 )
	{
		$name = trim($name);
		$original = $name;

		$chars = $chars < 10 ? 10 : $chars;

		if (strlen($name) > $chars)
		{
			$cutFront = $chars - 10;
			$name = substr($name, 0, $cutFront);
			$name = $name . '&#8230;';
			$name = $name . substr($original, -10, 10);
		}
		if ($name == '')
		{
			$name = '&#8230;';
		}

		return $name;
	}

	/**
	 * Makes file name safe to use
	 *
	 * @param string $file The name of the file [not full path]
	 * @return string The sanitized string
	 */
	public static function makeSafeFile($file)
	{
	//	$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#');
		return preg_replace($regex, '', $file);
	}

	/**
	 * Makes path name safe to use.
	 *
	 * @access	public
	 * @param	string The full path to sanitise.
	 * @return	string The sanitised string.
	 */
	public static function makeSafeDir($path)
	{
		$ds = (DS == '\\') ? '\\' . DS : DS;
		$regex = array('#[^A-Za-z0-9:\_\-' . $ds . ' ]#');
		return preg_replace($regex, '', $path);
	}

	/**
	 * Get admin notes
	 *
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public static function getAdminNotes($notes = '', $reviewer = '')
	{
		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);
		$ntext = '';
		if (count($matches) > 0)
		{
			$notes = $matches[0];
			if (count($notes) > 0)
			{
				krsort($notes);
				foreach ($notes as $match)
				{
					$ntext .= self::parseAdminNote($match, $reviewer);
				}
			}
		}

		return $ntext;
	}

	/**
	 * Get admin notes count
	 *
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public static function getAdminNoteCount($notes = '', $reviewer = '')
	{
		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);

		if (count($matches) > 0)
		{
			$notes = $matches[0];
			return count($notes);
		}

		return 0;
	}

	/**
	 * Parse admin notes
	 *
	 * @param      string $note
	 * @param      string $reviewer
	 * @param      boolean $showmeta
	 * @param      int $shorten
	 * @return     string
	 */
	public static function parseAdminNote($note = '', $reviewer = '', $showmeta = 1, $shorten = 0)
	{
		$note = str_replace('<nb:'.$reviewer.'>','', $note);
		$note = str_replace('</nb:'.$reviewer.'>','', $note);

		preg_match("#<meta>(.*?)</meta>#s", $note, $matches);
		if (count($matches) > 0)
		{
			$meta = $matches[0];
			$note   = preg_replace( '#<meta>(.*?)</meta>#s', '', $note );

			if ($shorten)
			{
				$note   = \Hubzero\Utility\String::truncate($note, $shorten);
			}
			if ($showmeta)
			{
				$meta = str_replace('<meta>','' , $meta);
				$meta = str_replace('</meta>','', $meta);

				$note  .= '<span class="block mini faded">' . $meta . '</span>';
			}
		}
		$note = $note ? '<p class="admin-note">' . $note . '</p>' : '';

		return $note;
	}

	/**
	 * Get last admin note
	 *
	 * @param      string $notes
	 * @param      string $reviewer
	 * @return     string
	 */
	public static function getLastAdminNote($notes = '', $reviewer = '')
	{
		$match = '';
		preg_match_all("#<nb:".$reviewer.">(.*?)</nb:".$reviewer.">#s", $notes, $matches);

		if (count($matches) > 0)
		{
			$notes = $matches[0];
			if (count($notes) > 0)
			{
				$match = self::parseAdminNote(end($notes), $reviewer, 1, 100);
			}
		}
		else
		{
			$match = '';
		}

		return $match;
	}

	/**
	 * Email
	 *
	 * @param      string $email
	 * @param      string $subject
	 * @param      string $body
	 * @param      array $from
	 * @return     void
	 */
	public static function email($email, $subject, $body, $from)
	{
		if ($from)
		{
			$body_plain = is_array($body) && isset($body['plaintext']) ? $body['plaintext'] : $body;
			$body_html  = is_array($body) && isset($body['multipart']) ? $body['multipart'] : NULL;

			$message = new \Hubzero\Mail\Message();
			$message->setSubject($subject)
				->addTo($email, $email)
				->addFrom($from['email'], $from['name'])
				->setPriority('normal');

			$message->addPart($body_plain, 'text/plain');

			if ($body_html)
			{
				$message->addPart($body_html, 'text/html');
			}

			$message->send();
			return true;
		}
		return false;
	}

	/**
	 * Get suggestions for new project members
	 *
	 * @param      object $project
	 * @param      string $option
	 * @param      int $uid
	 * @param      array $config
	 * @param      array $params
	 * @return     void
	 */
	public static function getSuggestions( $model )
	{
		$suggestions = array();

		$goto   = '&alias=' . $model->get('alias');
		$option = 'com_projects';
		$counts = $model->get('counts');

		// Adding a picture
		if ($model->access('owner') && !$model->get('picture'))
		{
			$suggestions[] = array(
				'class' => 's-picture',
				'text'  => Lang::txt('COM_PROJECTS_WELCOME_ADD_THUMB'),
				'url'   => Route::url('index.php?option=' . $option . $goto . '&task=edit')
			);
		}

		// Adding grant information
		if ($model->access('owner') && $model->config()->get('grantinfo')
			&& !$model->params->get('grant_title', ''))
		{
			$suggestions[] = array(
				'class' => 's-about',
				'text'  => Lang::txt('COM_PROJECTS_WELCOME_ADD_GRANT_INFO'),
				'url'   => Route::url('index.php?option=' . $option . $goto . '&task=edit&active=settings')
			);
		}

		// Adding about text
		if ($model->access('owner') && !$model->get('about') && $model->isPublic())
		{
			$suggestions[] = array(
				'class' => 's-about',
				'text'  => Lang::txt('COM_PROJECTS_WELCOME_ADD_ABOUT'),
				'url'   => Route::url('index.php?option=' . $option . $goto . '&task=edit')
			);
		}

		// File upload
		if (!empty($counts['files']) && $counts['files'] == 0)
		{
			$text = $model->access('owner')
				? Lang::txt('COM_PROJECTS_WELCOME_UPLOAD_FILES')
				: Lang::txt('COM_PROJECTS_WELCOME_SHARE_FILES');
			$suggestions[] = array(
				'class' => 's-files',
				'text'  => $text,
				'url'   => Route::url('index.php?option=' . $option . $goto . '&active=files')
			);
		}

		// Inviting others
		if ($model->access('manager'))
		{
			$suggestions[] = array(
				'class' => 's-team',
				'text'  => Lang::txt('COM_PROJECTS_WELCOME_INVITE_USERS'),
				'url'   => Route::url('index.php?option=' . $option . $goto . '&task=edit&active=team')
			);
		}

		// Todo items
		$suggestions[] = array(
			'class' => 's-todo',
			'text'  => Lang::txt('COM_PROJECTS_WELCOME_ADD_TODO'),
			'url'   => Route::url('index.php?option=' . $option . $goto . '&active=todo')
		);

		// Notes
		$suggestions[] = array(
			'class' => 's-notes',
			'text'  => Lang::txt('COM_PROJECTS_WELCOME_START_NOTE'),
			'url'   => Route::url('index.php?option=' . $option . $goto . '&active=notes')
		);

		return $suggestions;
	}

	/**
	 * Suggest alias name from title
	 *
	 * @param  string $title
	 * @return     void
	 */
	public static function suggestAlias ($title = '', $maxLength = 30)
	{
		if ($title)
		{
			$name = preg_replace('/ /', '', $title);
			$name = strtolower($name);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			$name = substr($name, 0, $maxLength);
			return $name;
		}
		return false;
	}

	/**
	 * Check file for viruses
	 *
	 * @param      string 	$fpath		Full path to scanned file
	 *
	 * @return     mixed
	 */
	public static function virusCheck( $fpath = '' )
	{
		jimport('joomla.filesystem.file');
		if (!\JFile::isSafe($fpath))
		{
			\JFile::delete($fpath);
			return true;
		}

		return false;
	}

	/**
	 * Get group members
	 *
	 * @param  string $groupname
	 * @return void
	 */
	public static function getGroupMembers($groupname)
	{
		$team = array();
		if ($groupname)
		{
			$group = \Hubzero\User\Group::getInstance($groupname);
			if ($group && $group->get('gidNumber'))
			{
				$members 	= $group->get('members');
				$managers 	= $group->get('managers');
				$team 		= array_merge($members, $managers);
				$team 		= array_unique($team);
			}
		}

		return $team;
	}

	/**
	 * Get tabs
	 *
	 * @return    array
	 */
	public static function getPluginNames( &$plugins )
	{
		// Make sure we have name and title
		$names = array();
		for ($i = 0, $n = count($plugins); $i <= $n; $i++)
		{
			if (empty($plugins[$i]) || !isset($plugins[$i]['name']))
			{
				unset($plugins[$i]);
			}
			else
			{
				$names[] = $plugins[$i]['name'];
			}
		}

		return array_unique($names);
	}

	/**
	 * Get active tabs
	 *
	 * @return    array
	 */
	public static function getTabs( &$plugins )
	{
		// Make sure we have name and title
		$tabs = array();
		for ($i = 0, $n = count($plugins); $i <= $n; $i++)
		{
			if (empty($plugins[$i]) || !isset($plugins[$i]['name']))
			{
				unset($plugins[$i]);
			}
			else
			{
				if (isset($plugins[$i]['show']) && $plugins[$i]['show'] == false)
				{
					continue;
				}
				if (!in_array($plugins[$i], $tabs))
				{
					$tabs[] = $plugins[$i];
				}
			}
		}

		return $tabs;
	}

	/**
	 * Get project Git repo path
	 *
	 * @param      string $projectAlias
	 * @param      string $case
	 * @return     string
	 */
	public static function getProjectRepoPath( $projectAlias = '', $case = 'files', $exists = true )
	{
		if (!trim($projectAlias))
		{
			return false;
		}

		// Get component config
		$config = Component::params('com_projects');

		// Build repo path
		$path   = DS . trim($config->get('webpath'), DS) . DS . strtolower(trim($projectAlias));
		$path  .= $case ? DS . $case : '';
		if (!$config->get('offroot', 0))
		{
			$path = PATH_APP . $path;
		}
		return (is_dir($path) || $exists == false) ? $path : false;
	}

	/**
	 * Build breadcrumbs inside files plugin
	 *
	 * @param      string $dir
	 * @param      string $url
	 * @return     string
	 */
	public static function buildFileBrowserCrumbs( $dir = '', $url = '', &$parent = NULL, $linkit = true)
	{
		$bc = NULL;
		$href = '';

		$desectPath = explode(DS, $dir);

		if ($dir && count($desectPath) > 0)
		{
			for ($p = 0; $p < count($desectPath); $p++)
			{
				$parent   = count($desectPath) > 1 && $p != count($desectPath)  ? $href  : '';
				$href  	 .= DS . $desectPath[$p];
				if ($linkit)
				{
					$bc .= ' &raquo; <span><a href="' . $url . '/?subdir='
						. urlencode($href) . '" class="folder">' . $desectPath[$p] . '</a></span> ';
				}
				else
				{
					$bc .= ' <span class="folder">' . $desectPath[$p] . '</span> &raquo; ';
				}
			}
		}

		return $bc;
	}

	/**
	 * Send hub message
	 *
	 * @param      string 	$option
	 * @param      object 	$project    Models\Project
	 * @param      array 	$addressees
	 * @param      string 	$subject
	 * @param      string 	$component
	 * @param      string 	$layout
	 * @param      string 	$message
	 * @param      string 	$reviewer
	 * @return     void
	 */
	public static function sendHUBMessage(
		$option, $project,
		$addressees = array(), $subject = '',
		$component = '', $layout = '',
		$message = '', $reviewer = '')
	{
		if (!$layout || !$subject || !$component || empty($addressees))
		{
			return false;
		}

		// Is messaging turned on?
		if ($project->config()->get('messaging') != 1)
		{
			return false;
		}

		// Set up email config
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt('COM_PROJECTS');
		$from['email'] = Config::get('mailfrom');

		// Html email
		$from['multipart'] = md5(date('U'));

		// Get message body
		$eview          = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site',
			'name'      => 'emails',
			'layout'    => $layout . '_plain'
		));

		$eview->option 			= $option;
		$eview->project 		= $project;
		$eview->message			= $message;
		$eview->reviewer		= $reviewer;

		$body = array();
		$body['plaintext'] 	= $eview->loadTemplate();
		$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

		// HTML email
		$eview->setLayout($layout . '_html');
		$body['multipart'] = $eview->loadTemplate();
		$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

		// Send HUB message
		Event::trigger( 'xmessage.onSendMessage',
			array(
				$component,
				$subject,
				$body,
				$from,
				$addressees,
				$option
			)
		);
	}
}
