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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Utility methods
 */
class ResourcesUtilities
{
	/**
	 * Cleans, normalizes, and constructs full path to media directory
	 *
	 * @param      string $dir Primary directory for media
	 * @param      string $subdir  Sub-directory of primary (optional)
	 * @return     string Return full system path
	 */
	public static function buildUploadPath($dir, $subdir='')
	{
		$config = JComponentHelper::getParams('com_resources');

		if ($subdir)
		{
			// Normalize path
			$subdir = ResourcesUtilities::normalizePath($subdir);
		}

		// Get the configured upload path
		$base = $config->get('uploadpath', DS . 'site' . DS . 'resources');
		$base = ResourcesUtilities::normalizePath($base);

		// Normalize path
		$dir = ResourcesUtilities::normalizePath($dir);

		// Does the beginning of the $dir match the config path?
		if (substr($dir, 0, strlen($base)) == $base)
		{
			// Yes - ... this really shouldn't happen
			JError::raiseError(500, JText::_('Paths match.'));
			return;
		}
		else
		{
			// No - append it
			$dir = $base . $dir;
		}

		// Build the path
		return JPATH_ROOT . $dir . $subdir;
	}

	/**
	 * Strips trailing slashes and ensures path begins with a slash
	 *
	 * @param      string $path Path to normalize
	 * @return     string
	 */
	public static function normalizePath($path)
	{
		jimport('joomla.filesystem.path');

		$path = JPath::clean($path);

		// Make sure the path doesn't end with a slash
		$path = rtrim($path, DS);

		// Ensure the path starts with a slash
		$path = ltrim($path, DS);
		$path = DS . $path;

		return $path;
	}
}

