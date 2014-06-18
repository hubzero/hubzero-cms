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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'middleware.php');

/**
 * Controller class for tools (default)
 */
class ToolsControllerZones extends \Hubzero\Component\SiteController
{
	/**
	 * Page Not found
	 *
	 * @return    exit
	 */
	public function notFoundTask()
	{
		ob_end_clean();
		header("HTTP/1.1 404 Not Found");
		echo "Not Found";
		ob_end_flush();
		exit;
	}

	/**
	 * Normalize a path
	 *
	 * @param     string  $path
	 * @param     boolean $isFile
	 * @return    mixed
	 */
	private static function normalize_path($path, $isFile = false)
	{
		if (!isset($path[0]) || $path[0] != '/')
		{
			return false;
		}

		$parts = explode('/', $path);

		$result = array();

		foreach ($parts as $part)
		{
			if ($part === '' || $part == '.')
			{
				continue;
			}

			if ($part == '..')
			{
				array_pop($result);
			}
			else
			{
				$result[] = $part;
			}
		}

		if ($isFile) // Files can't end with directory separator or special directory names
		{
			if ($part == '' || $part == '.' || $part == '..')
			{
				return false;
			}
		}

		return '/' . implode('/', $result) . ($isFile ? '' : '/');
	}

	/**
	 * Asset delivery function.
	 *
	 * @return    exit
	 */
	public function assetsTask()
	{
		$file = JRequest::getVar('file');
		$file = self::normalize_path('/' . trim($file, '/'), true);

		if (empty($file))
		{
			echo 'file:' . $file; die();
			return $this->notFoundTask();
		}

		$zone = new MiddlewareModelZone(JRequest::getInt('id', 0));
		if (!$zone->exists())
		{
			echo 'zone: ' . $zone->get('id'); die();
			return $this->notFoundTask();
		}

		$file = $zone->logo('path') . '/' . ltrim($file, '/');

		if (!is_file($file) || !is_readable($file))
		{
			return $this->notFoundTask();
		}

		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			JError::raiseError(404, JText::_('COM_TOOLS_SERVER_ERROR'));
		}

		exit;
	}
}

