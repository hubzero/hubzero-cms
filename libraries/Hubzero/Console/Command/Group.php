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
class Group implements CommandInterface
{
	/**
	 * Output object, implements the Output interface
	 *
	 * @var object
	 **/
	private $output;

	/**
	 * Arguments object, implements the Argument interface
	 *
	 * @var object
	 **/
	private $arguments;

	/**
	 * Group object
	 *
	 * @var object
	 **/
	private $group;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		$this->output    = $output;
		$this->arguments = $arguments;

		// do we have a group arg?
		if ($cname = $this->arguments->getOpt('group'))
		{
			$group = \Hubzero\User\Group::getInstance($cname);
		}
		else
		{
			// get the current directory
			$currentDirectory = getcwd();

			// remove web root
			$currentDirectory = str_replace(JPATH_ROOT, '', $currentDirectory);

			// Get group upload directory
			$groupsConfig     = \JComponentHelper::getParams('com_groups');
			$groupsDirectory  = trim($groupsConfig->get('uploadpath', '/site/groups'), DS);

			// are we within the groups upload path
			if (strpos($currentDirectory, $groupsDirectory))
			{
				$gid = str_replace($groupsDirectory, '', $currentDirectory);
				$gid = trim($gid, DS);

				// get group instance
				$group = \Hubzero\User\Group::getInstance($gid);
			}
		}

		// make sure we have a group & its super!
		if ($group && $group->isSuperGroup())
		{
			$this->group = $group;
		}
		else
		{
			$this->output->error('Error: Provided group is not valid');
		}
	}

	/**
	 * Default (required) command - just executes run
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Run super groups scaffolding
	 *
	 * @return void
	 **/
	public function scaffolding()
	{
		// Get group config
		$groupsConfig = \JComponentHelper::getParams('com_groups');

		// Path to group folder
		$directory  = trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
		$directory .= DS . $this->group->get('gidNumber') . DS . 'components';

		// set our needed args
		$this->arguments->setOpt(3, 'component');
		$this->arguments->setOpt('install-dir', $directory);
		Application::call('scaffolding', 'create', $this->arguments, $this->output);
	}

	/**
	 * Run super groups migration
	 * 
	 * @return [type] [description]
	 */
	public function migrate()
	{
		// set our group arg & call migration
		$this->arguments->setOpt('group', $this->group->get('cn'));
		Application::call('migration', 'run', $this->arguments, $this->output);
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Super group management commands.'
			);
	}
}