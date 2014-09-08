<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Application;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Group command class
 **/
class Cache extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
		return;
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('Cache Management.')
		     ->render();
	}

	/**
	 * Clear all Cache
	 * @return [type] [description]
	 */
	public function clearAllCache()
	{
		// path to cache folder
		$cacheDir = JPATH_ROOT . DS . 'cache' . DS . '*';

		// remove recursively
		foreach (glob($cacheDir) as $cacheFileOrDir)
		{
			$readable = str_replace(JPATH_ROOT, '', $cacheFileOrDir);
			if (is_dir($cacheFileOrDir))
			{
				if (!@rmdir($cacheFileOrDir))
				{
					$this->output->addLine('Unable to delete cache directory: ' . $readable, 'error');
				}
			}
			else
			{
				if (!@unlink($cacheFileOrDir))
				{
					$this->output->addLine('Unable to delete cache file: ' . $readable, 'error');
				}
			}
		}
	}

	/**
	 * Clear Site.css & Site.less.cache files
	 * 
	 * @return [type] [description]
	 */
	public function clearCssCache()
	{
		$cacheDir = JPATH_ROOT . DS . 'cache';
		$files = array('site.css', 'site.less.cache');

		// remove each file
		foreach ($files as $file)
		{
			if (!unlink($cacheDir . DS . $file))
			{
				$this->output->addLine('Unable to delete cache file: ' . $file, 'error');
			}
		}

		// success!
		$this->output->addLine('CSS cache files removed', 'success');
	}
}