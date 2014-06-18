<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined('n')) {

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

class ProjectsHtml
{
	/**
	 * Short description for 'error'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Get project thumbnail
	 *
	 * @param      int $id
	 * @param      string $alias
	 * @param      string $picname
	 * @param      array $config
	 * @return     string HTML
	 */
	public static function getThumbSrc( $id, $alias, $picname = '', $config )
	{
		$src  = '';
		$path = DS . trim($config->get('imagepath', '/site/projects'), DS) . DS . $alias . DS . 'images';

		if (file_exists( JPATH_ROOT . $path . DS . 'thumb.png' ))
		{
			return $path . DS . 'thumb.png';
		}

		if ($picname)
		{
			$thumb = ProjectsHtml::createThumbName($picname);
			$src = $thumb && file_exists( JPATH_ROOT . $path . DS . $thumb ) ? $path . DS . $thumb :  '';
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
	public static function createThumbName( $image=null, $tn='_thumb', $ext = '' )
	{
		if (!$image)
		{
			$this->setError( JText::_('No image set.') );
			return false;
		}

		$image = explode('.',$image);
		$n = count($image);

		if ($n > 1)
		{
			$image[$n-2] .= $tn;
			$end = array_pop($image);
			if ($ext)
			{
				$image[] = $ext;
			}
			else
			{
				$image[] = $end;
			}

			$thumb = implode('.',$image);
		}
		else
		{
			// No extension
			$thumb = $image[0];
			$thumb .= $tn;
			if ($ext)
			{
				$thumb .= '.'.$ext;
			}
		}
		return $thumb;
	}

	//----------------------------------------------------------
	// Date/time management
	//----------------------------------------------------------

	/**
	 * Time elapsed from moment
	 *
	 * @param      string $timestamp
	 * @return     string
	 */
	public static function timeAgo($timestamp)
	{
		$text = JHTML::_('date.relative', $timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		if ($text == '0 seconds')
		{
			$text = JText::_('COM_PROJECTS_JUST_A_MOMENT');
		}

		return $text;
	}

	//----------------------------------------------------------
	// File management
	//----------------------------------------------------------

	/**
	 * Get file attributes
	 *
	 * @param      string $path
	 * @param      string $base_path
	 * @param      string $get
	 * @param      string $prefix
	 * @return     string
	 */
	public static function getFileAttribs( $path = '', $base_path = '', $get = '', $prefix = JPATH_ROOT )
	{
		if (!$path)
		{
			return '';
		}

		// Get extension
		if ($get == 'ext')
		{
			$ext = explode('.', basename($path));
			$ext = count($ext) > 1 ? end($ext) : '';
			return strtoupper($ext);
		}

		$path = DS . trim($path, DS);
		if ($base_path)
		{
			$base_path = DS . trim($base_path, DS);
		}

		if (substr($path, 0, strlen($base_path)) == $base_path)
		{
			// Do nothing
		}
		else
		{
			$path = $base_path . $path;
		}
		$path = $prefix . $path;

		$fs = '';

		// Get the file size if the file exist
		if (file_exists( $path ))
		{
			try
			{
				$fs = filesize( $path );
			}
			catch (Exception $e)
			{
				// could not get file size
			}
		}
		$fs = ProjectsHtml::formatSize($fs);
		return ($fs) ? $fs : '';
	}

	/**
	 * Format size
	 *
	 * @param      int $file_size
	 * @param      int $round
	 * @return     string
	 */
	public static function formatSize($file_size, $round = 0)
	{
		if ($file_size >= 1073741824)
		{
			$file_size = round(($file_size / 1073741824 * 100), $round) / 100 . 'GB';
		}
		elseif ($file_size >= 1048576)
		{
			$file_size = round(($file_size / 1048576 * 100), $round) / 100 . 'MB';
		}
		elseif ($file_size >= 1024)
		{
			$file_size = round(($file_size / 1024 * 100) / 100, $round) . 'KB';
		}
		elseif ($file_size < 1024)
		{
			$file_size = $file_size . 'b';
		}

		return $file_size;
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

		if($from == 'b')
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
}
