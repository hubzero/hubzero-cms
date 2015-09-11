<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Group command class
 **/
class Group extends Base implements CommandInterface
{
	/**
	 * Group object
	 *
	 * @var  object
	 **/
	private $group;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
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
			$currentDirectory = str_replace(PATH_APP, '', $currentDirectory);

			// Get group upload directory
			$groupsConfig     = \Component::params('com_groups');
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
	 * @return  void
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
	 * @return  void
	 **/
	public function scaffolding()
	{
		// Get group config
		$groupsConfig = \Component::params('com_groups');

		// Path to group folder
		$directory  = trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
		$directory .= DS . $this->group->get('gidNumber');

		// Determine what we want to create
		$createWhat = ($this->arguments->getOpt(3)) ? $this->arguments->getOpt(3) : 'component';

		// Set our needed args
		$this->arguments->setOpt(3, $createWhat);
		$this->arguments->setOpt('install-dir', $directory);
		App::get('client')->call('scaffolding', 'create', $this->arguments, $this->output);
	}

	/**
	 * Run super groups migration
	 * 
	 * @return  void
	 */
	public function migrate()
	{
		// Set our group arg & call migration
		$this->arguments->setOpt('group', $this->group->get('cn'));
		App::get('client')->call('migration', 'run', $this->arguments, $this->output);
	}

	/**
	 * Update super group code
	 * 
	 * @return  void
	 */
	public function update()
	{
		// Get group config
		$groupsConfig = \Component::params('com_groups');

		// Path to group folder
		$directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
		$directory .= DS . $this->group->get('gidNumber');

		// Get task, defaults to update
		$task = ($this->arguments->getOpt(3)) ? $this->arguments->getOpt(3) : 'update';

		// Set our group directory & call update
		$this->arguments->setOpt('r', $directory);
		App::get('client')->call('repository', $task, $this->arguments, $this->output);
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
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