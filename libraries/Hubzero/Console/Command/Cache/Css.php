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

namespace Hubzero\Console\Command\Cache;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Cache command class
 **/
class Css extends Base implements CommandInterface
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
		     ->render();
	}

	/**
	 * Clear Site.css & Site.less.cache files
	 * 
	 * @return void
	 */
	public function clear()
	{
		$cacheDir = JPATH_ROOT . DS . 'cache';
		$files = array('site.css', 'site.less.cache');

		// Remove each file
		foreach ($files as $file)
		{
			if (!is_file($cacheDir . DS . $file))
			{
				$this->output->addLine($file . ' does not exist', 'warning');
				continue;
			}

			if (!unlink($cacheDir . DS . $file))
			{
				$this->output->addLine('Unable to delete cache file: ' . $file, 'error');
			}
			else
			{
				$this->output->addLine($file . ' deleted', 'success');
			}
		}

		// success!
		$this->output->addLine('All CSS cache files removed!', 'success');
	}
}