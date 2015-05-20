<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html\Builder;

use Hubzero\Utility\Arr;
use App;

/**
 * Utility class for pushing assets to the document
 */
class Asset
{
	/**
	 * Compute the files to be include
	 *
	 * @param   string   $folder          folder name to search into (images, css, js, ...)
	 * @param   string   $file            path to file
	 * @param   boolean  $relative        path to file is relative to /media folder
	 * @param   boolean  $detect_browser  detect browser to include specific browser files
	 * @param   boolean  $detect_debug    detect debug to include compressed files if debug is on
	 * @return  array    files to be included
	 */
	protected static function includeRelativeFiles($folder, $file, $relative, $detect_browser, $detect_debug)
	{
		// If http is present in filename
		if (strpos($file, 'http') === 0)
		{
			$includes = array($file);
		}
		else
		{
			// Extract extension and strip the file
			$strip = App::get('filesystem')->name($file);
			$ext   = App::get('filesystem')->extension($file);

			// Detect browser and compute potential files
			if ($detect_browser)
			{
				$navigator = new \Hubzero\Browser\Detector();
				$browser = $navigator->name();
				$major   = $navigator->major();
				$minor   = $navigator->minor();

				// Try to include files named filename.ext, filename_browser.ext, filename_browser_major.ext, filename_browser_major_minor.ext
				// where major and minor are the browser version names
				$potential = array(
					$strip,
					$strip . '_' . $browser,
					$strip . '_' . $browser . '_' . $major,
					$strip . '_' . $browser . '_' . $major . '_' . $minor
				);
			}
			else
			{
				$potential = array($strip);
			}

			// If relative search in template directory or media directory
			if ($relative)
			{
				// Get the template
				$template = App::get('template')->template;

				// Prepare array of files
				$includes = array();

				// For each potential files
				foreach ($potential as $strip)
				{
					$files = array();
					// Detect debug mode
					if ($detect_debug && App::get('config')->get('debug'))
					{
						$files[] = $strip . '-uncompressed.' . $ext;
					}
					$files[] = $strip . '.' . $ext;

					// Loop on 1 or 2 files and break on first found
					foreach ($files as $file)
					{
						// If the file is in the template folder
						if (file_exists(JPATH_THEMES . "/$template/$folder/$file"))
						{
							$includes[] = JURI::base(true) . "/templates/$template/$folder/$file";
							break;
						}
						else
						{
							// If the file contains any /: it can be in an media extension subfolder
							if (strpos($file, '/'))
							{
								// Divide the file extracting the extension as the first part before /
								list($extension, $file) = explode('/', $file, 2);

								// If the file yet contains any /: it can be a plugin
								if (strpos($file, '/'))
								{
									// Divide the file extracting the element as the first part before /
									list($element, $file) = explode('/', $file, 2);

									// Try to deal with plugins group in the media folder
									if (file_exists(JPATH_ROOT . "/media/$extension/$element/$folder/$file"))
									{
										$includes[] = JURI::root(true) . "/media/$extension/$element/$folder/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/$extension/$element/$folder/$file");
										break;
									}
									// Try to deal with classical file in a a media subfolder called element
									elseif (file_exists(JPATH_ROOT . "/media/$extension/$folder/$element/$file"))
									{
										$includes[] = JURI::root(true) . "/media/$extension/$folder/$element/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/$extension/$folder/$element/$file");
										break;
									}
									// Try to deal with system files in the template folder
									elseif (file_exists(JPATH_THEMES . "/$template/$folder/system/$element/$file"))
									{
										$includes[] = JURI::root(true) . "/templates/$template/$folder/system/$element/$file" . '?v=' . filemtime(JPATH_THEMES . "/$template/$folder/system/$element/$file");
										break;
									}
									// Try to deal with system files in the media folder
									elseif (file_exists(JPATH_ROOT . "/media/system/$folder/$element/$file"))
									{
										$includes[] = JURI::root(true) . "/media/system/$folder/$element/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/system/$folder/$element/$file");
										break;
									}
								}
								// Try to deals in the extension media folder
								elseif (file_exists(JPATH_ROOT . "/media/$extension/$folder/$file"))
								{
									$includes[] = JURI::root(true) . "/media/$extension/$folder/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/$extension/$folder/$file");
									break;
								}
								// Try to deal with system files in the template folder
								elseif (file_exists(JPATH_THEMES . "/$template/$folder/system/$file"))
								{
									$includes[] = JURI::root(true) . "/templates/$template/$folder/system/$file" . '?v=' . filemtime(JPATH_THEMES . "/$template/$folder/system/$file");
									break;
								}
								// Try to deal with system files in the media folder
								elseif (file_exists(JPATH_ROOT . "/media/system/$folder/$file"))
								{
									$includes[] = JURI::root(true) . "/media/system/$folder/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/system/$folder/$file");
									break;
								}
							}
							// Try to deal with system files in the media folder
							elseif (file_exists(JPATH_ROOT . "/media/system/$folder/$file"))
							{
								$includes[] = JURI::root(true) . "/media/system/$folder/$file" . '?v=' . filemtime(JPATH_ROOT . "/media/system/$folder/$file");
								break;
							}
						}
					}
				}
			}
			// If not relative and http is not present in filename
			else
			{
				$includes = array();

				foreach ($potential as $strip)
				{
					// Detect debug mode
					if ($detect_debug && App::get('config')->get('debug') && file_exists(PATH_ROOT . "/$strip-uncompressed.$ext"))
					{
						$includes[] = App::get('request')->root(true) . "/$strip-uncompressed.$ext";
					}
					elseif (file_exists(PATH_ROOT . "/$strip.$ext"))
					{
						$includes[] = App::get('request')->root(true) . "/$strip.$ext";
					}
				}
			}
		}
		return $includes;
	}

	/**
	 * Write a <img /> element
	 *
	 * @param   string   $file       The relative or absolute URL to use for the src attribute
	 * @param   string   $alt        The alt text.
	 * @param   string   $attribs    The target attribute to use
	 * @param   array    $relative   An associative array of attributes to add
	 * @param   boolean  $path_only  If set to true, it tries to find an override for the file in the template
	 * @return  string
	 */
	public static function image($file, $alt, $attribs = null, $relative = false, $path_only = false)
	{
		if (is_array($attribs))
		{
			$attribs = Arr::toString($attribs);
		}

		$includes = self::includeRelativeFiles('images', $file, $relative, false, false);

		// If only path is required
		if ($path_only)
		{
			if (count($includes))
			{
				return $includes[0];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return '<img src="' . (count($includes) ? $includes[0] : '') . '" alt="' . $alt . '" ' . $attribs . ' />';
		}
	}

	/**
	 * Write a <link rel="stylesheet" style="text/css" /> element
	 *
	 * @param   string   $file            path to file
	 * @param   array    $attribs         attributes to be added to the stylesheet
	 * @param   boolean  $relative        path to file is relative to /media folder
	 * @param   boolean  $path_only       return the path to the file only
	 * @param   boolean  $detect_browser  detect browser to include specific browser css files
	 *                                    will try to include file, file_*browser*, file_*browser*_*major*, file_*browser*_*major*_*minor*
	 *                                    <table>
	 *                                       <tr><th>Navigator</th>                  <th>browser</th>	<th>major.minor</th></tr>
	 *
	 *                                       <tr><td>Safari 3.0.x</td>               <td>konqueror</td>	<td>522.x</td></tr>
	 *                                       <tr><td>Safari 3.1.x and 3.2.x</td>     <td>konqueror</td>	<td>525.x</td></tr>
	 *                                       <tr><td>Safari 4.0 to 4.0.2</td>        <td>konqueror</td>	<td>530.x</td></tr>
	 *                                       <tr><td>Safari 4.0.3 to 4.0.4</td>      <td>konqueror</td>	<td>531.x</td></tr>
	 *                                       <tr><td>iOS 4.0 Safari</td>             <td>konqueror</td>	<td>532.x</td></tr>
	 *                                       <tr><td>Safari 5.0</td>                 <td>konqueror</td>	<td>533.x</td></tr>
	 *
	 *                                       <tr><td>Google Chrome 1.0</td>          <td>konqueror</td>	<td>528.x</td></tr>
	 *                                       <tr><td>Google Chrome 2.0</td>          <td>konqueror</td>	<td>530.x</td></tr>
	 *                                       <tr><td>Google Chrome 3.0 and 4.x</td>  <td>konqueror</td>	<td>532.x</td></tr>
	 *                                       <tr><td>Google Chrome 5.0</td>          <td>konqueror</td>	<td>533.x</td></tr>
	 *
	 *                                       <tr><td>Internet Explorer 5.5</td>      <td>msie</td>		<td>5.5</td></tr>
	 *                                       <tr><td>Internet Explorer 6.x</td>      <td>msie</td>		<td>6.x</td></tr>
	 *                                       <tr><td>Internet Explorer 7.x</td>      <td>msie</td>		<td>7.x</td></tr>
	 *                                       <tr><td>Internet Explorer 8.x</td>      <td>msie</td>		<td>8.x</td></tr>
	 *
	 *                                       <tr><td>Firefox</td>                    <td>mozilla</td>	<td>5.0</td></tr>
	 *                                    </table>
	 *                                    a lot of others
	 * @param   boolean  $detect_debug    detect debug to search for compressed files if debug is on
	 * @return  mixed    nothing if $path_only is false, null, path or array of path if specific css browser files were detected
	 */
	public static function stylesheet($file, $attribs = array(), $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
	{
		$includes = self::includeRelativeFiles('css', $file, $relative, $detect_browser, $detect_debug);

		// If only path is required
		if ($path_only)
		{
			if (count($includes) == 0)
			{
				return null;
			}
			elseif (count($includes) == 1)
			{
				return $includes[0];
			}
			else
			{
				return $includes;
			}
		}
		// If inclusion is required
		else
		{
			$document = App::get('document');
			foreach ($includes as $include)
			{
				$document->addStylesheet($include, 'text/css', null, $attribs);
			}
		}
	}

	/**
	 * Write a <script></script> element
	 *
	 * @param   string   $file            path to file
	 * @param   boolean  $framework       load the JS framework
	 * @param   boolean  $relative        path to file is relative to /media folder
	 * @param   boolean  $path_only       return the path to the file only
	 * @param   boolean  $detect_browser  detect browser to include specific browser js files
	 * @param   boolean  $detect_debug    detect debug to search for compressed files if debug is on
	 * @return  mixed  nothing if $path_only is false, null, path or array of path if specific js browser files were detected
	 */
	public static function script($file, $framework = false, $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
	{
		if ($framework)
		{
			Behavior::framework();
		}

		$includes = self::includeRelativeFiles('js', $file, $relative, $detect_browser, $detect_debug);

		// If only path is required
		if ($path_only)
		{
			if (count($includes) == 0)
			{
				return null;
			}
			elseif (count($includes) == 1)
			{
				return $includes[0];
			}
			else
			{
				return $includes;
			}
		}
		// If inclusion is required
		else
		{
			$document = App::get('document');
			foreach ($includes as $include)
			{
				$document->addScript($include);
			}
		}
	}
}
