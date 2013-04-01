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

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for support tickets
 */
class plgCronCache extends JPlugin
{
	/**
	 * Return a list of events
	 * 
	 * @return     array
	 */
	public function onCronEvents()
	{
		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			'cleanSystemCss' => JText::_('Remove old system CSS files')
		);

		return $obj;
	}

	/**
	 * Calculate point royalties for members
	 * 
	 * @return     array
	 */
	public function cleanSystemCss()
	{
		$path = JPATH_ROOT . DS . 'cache';

		if (!is_dir($path))
		{
			return;
		}

		$docs = array();
		jimport('joomla.filesystem.file');

		$dirIterator = new DirectoryIterator($path);
		foreach ($dirIterator as $file)
		{
			if ($file->isDot() || $file->isDir())
			{
				continue;
			}

			if ($file->isFile())
			{
				$name = $file->getFilename();

				$ext = JFile::getExt($name);

				if (('cvs' == strtolower($name))
				 || ('.svn' == strtolower($name))
				 || ($ext != 'css'))
				{
					continue;
				}

				if (substr($name, 0, strlen('system-')) != 'system-')
				{
					continue;
				}

				$docs[$path . DS . $name] = $name;
			}
		}

		if (count($docs) > 1)
		{
			//$last = array_pop($docs);

			foreach ($docs as $p => $n)
			{
				JFile::delete($p);
			}
		}

		return true;
	}
}

