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
use Hubzero\Content\Migration\Base as Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Extension class
 **/
class Extension extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return void
	 **/
	public function execute()
	{
		if ($this->output->isInteractive())
		{
			$actions = array('add', 'delete', 'enable', 'disable');
			$action  = $this->output->getResponse('What do you want to do? ['.implode("|", $actions).']');

			if (in_array($action, $actions))
			{
				$this->$action();
			}
			else
			{
				$this->output->error('Sorry, I don\'t know how to do that.');
			}
		}
		else
		{
			$this->output = $this->output->getHelpOutput();
			$this->help();
			$this->output->render();
			return;
		}
	}

	/**
	 * Add an entry to the extension table
	 *
	 * @return void
	 **/
	public function add()
	{
		$this->alter('add');
	}

	/**
	 * Delete an entry from the extension table
	 *
	 * @return void
	 **/
	public function delete()
	{
		$this->alter('delete');
	}

	/**
	 * Alter extension
	 *
	 * @param  string - method name
	 * @return void
	 **/
	private function alter($method)
	{
		$database  = \JFactory::getDbo();
		$migration = new Migration($database);

		$name = null;
		if ($this->arguments->getOpt('name'))
		{
			$name = $this->arguments->getOpt('name');
		}

		if (!isset($name))
		{
			if ($this->output->isInteractive())
			{
				$name = $this->output->getResponse("What extension were you wanting to {$method}?");
			}
			else
			{
				$this->output = $this->output->getHelpOutput();
				$this->help();
				$this->output->render();
				return;
			}
		}

		$extensionType = substr($name, 0, 3);

		switch ($extensionType)
		{
			case 'com':
				$extensionName = ucfirst(substr($name, 4));
				$mthd          = $method . 'ComponentEntry';
				$migration->$mthd($extensionName);
				break;

			case 'mod':
				$mthd = $method . 'ModuleEntry';
				$migration->$mthd($name);
				break;

			case 'plg':
				preg_match('/plg_([[:alnum:]]+)_([[:alnum:]]*)/', $name, $matches);

				if (!isset($matches[1]) || !isset($matches[2]))
				{
					$this->output->error('This does not appear to be a valid extension name.');
				}

				$folder  = $matches[1];
				$element = $matches[2];
				$mthd    = $method . 'PluginEntry';
				$migration->$mthd($folder, $element);
				break;

			default:
				$this->output->error('This does not appear to be a valid extension name.');
			break;
		}

		$this->output->addLine("Successfully {$method}ed {$name}!", 'success');
	}

	/**
	 * Enable an extension
	 *
	 * @return void
	 **/
	public function enable()
	{
		$this->output->addLine('Not implemented', 'warning');
	}

	/**
	 * Disable an extension
	 *
	 * @return void
	 **/
	public function disable()
	{
		$this->output->addLine('Not implemented', 'warning');
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
				'Extension management utility functions.'
			)
			->addArgument(
				'--name: extension name',
				'The name of the extension with which you want to work.',
				'Example: --name=com_awesome'
			);
	}
}