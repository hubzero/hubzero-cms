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
class Group extends Base implements CommandInterface
{
	/**
	 * Group object
	 *
	 * @var object
	 **/
	private $group;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param  object - output renderer
	 * @param  object - command arguments
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);

		// Do we have a group arg?
		if ($cname = $this->arguments->getOpt('group'))
		{
			$group = \Hubzero\User\Group::getInstance($cname);
		}
		else
		{
			// Get the current directory
			$currentDirectory = getcwd();

			// Remove web root
			$currentDirectory = str_replace(JPATH_ROOT, '', $currentDirectory);

			// Get group upload directory
			$groupsConfig     = \JComponentHelper::getParams('com_groups');
			$groupsDirectory  = trim($groupsConfig->get('uploadpath', '/site/groups'), DS);

			// Are we within the groups upload path
			if (strpos($currentDirectory, $groupsDirectory))
			{
				$gid = str_replace($groupsDirectory, '', $currentDirectory);
				$gid = trim($gid, DS);

				// Get group instance
				$group = \Hubzero\User\Group::getInstance($gid);
			}
		}

		// Make sure we have a group & its super!
		if (isset($group) && $group && $group->isSuperGroup())
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
		$directory .= DS . $this->group->get('gidNumber');

		// Determine what we want to create
		$createWhat = ($this->arguments->getOpt(3)) ? $this->arguments->getOpt(3) : 'component';

		// Set our needed args
		$this->arguments->setOpt(3, $createWhat);
		$this->arguments->setOpt('install-dir', $directory);
		Application::call('scaffolding', 'create', $this->arguments, $this->output);
	}

	/**
	 * Run super groups migration
	 * 
	 * @return void
	 */
	public function migrate()
	{
		// Set our group arg & call migration
		$this->arguments->setOpt('group', $this->group->get('cn'));
		Application::call('migration', 'run', $this->arguments, $this->output);
	}

	/**
	 * Update super group code
	 * 
	 * @return void
	 */
	public function update()
	{
		// Get group config
		$groupsConfig = \JComponentHelper::getParams('com_groups');

		// Path to group folder
		$directory  = JPATH_ROOT . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
		$directory .= DS . $this->group->get('gidNumber');

		// Get task, defaults to update
		$task = ($this->arguments->getOpt(3)) ? $this->arguments->getOpt(3) : 'update';

		// Set our group directory & force mode & call update
		$this->arguments->setOpt('r', $directory);
		$this->arguments->setOpt('f', 1);
		Application::call('repository', $task, $this->arguments, $this->output);
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