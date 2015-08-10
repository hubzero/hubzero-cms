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

/**
 * Cache command class
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
		     ->addOverview('Cache Management')
		     ->addTasks($this)
		     ->render();
	}

	/**
	 * Clear all Cache
	 *
	 * @return void
	 *
	 * @museDescription Clears all cached items in document root cache directory
	 */
	public function clear()
	{
		// Path to cache folder
		$cacheDir = PATH_APP . DS . 'cache' . DS . '*';

		// Remove recursively
		foreach (glob($cacheDir) as $cacheFileOrDir)
		{
			$readable = str_replace(PATH_APP . DS, '', $cacheFileOrDir);
			if (is_dir($cacheFileOrDir))
			{
				if (!Filesystem::deleteDirectory($cacheFileOrDir))
				{
					$this->output->addLine('Unable to delete cache directory: ' . $readable, 'error');
				}
				else
				{
					$this->output->addLine($readable . ' deleted', 'success');
				}
			}
			else
			{
				// Don't delete index.html
				if ($cacheFileOrDir != PATH_APP . DS . 'cache' . DS . 'index.html')
				{
					if (!Filesystem::delete($cacheFileOrDir))
					{
						$this->output->addLine('Unable to delete cache file: ' . $readable, 'error');
					}
					else
					{
						$this->output->addLine($readable . ' deleted', 'success');
					}
				}
			}
		}

		$this->output->addLine('Clear cache complete', 'success');
	}
}