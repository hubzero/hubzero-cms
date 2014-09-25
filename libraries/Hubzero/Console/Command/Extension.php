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
			$actions = array('add', 'delete', 'install', 'enable', 'disable');
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
	 * Install an extension
	 *
	 * @return void
	 **/
	public function install()
	{
		$this->alter('install');
	}

	/**
	 * Enable an extension
	 *
	 * @return void
	 **/
	public function enable()
	{
		$this->alter('enable');
	}

	/**
	 * Disable an extension
	 *
	 * @return void
	 **/
	public function disable()
	{
		$this->alter('disable');
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
				if ($method == 'add' || $method == 'delete')
				{
					$extensionName = ucfirst(substr($name, 4));
					$mthd          = $method . 'ComponentEntry';
				}
				else
				{
					$extensionName = $name;
					$mthd          = $method . 'Component';
				}

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, components do not currently support the ' . $mthd . ' method');
				}

				$migration->$mthd($extensionName);
				break;

			case 'mod':
				if ($method == 'install')
				{
					$mthd     = $method . 'Module';
					$position = $this->arguments->getOpt('position', null);

					if (!isset($position))
					{
						if ($this->output->isInteractive())
						{
							$position = $this->output->getResponse("Where should the module be positioned?");
						}
						else
						{
							$this->output->addLine('Please provide a position for the module', 'warning');
							return;
						}
					}

					if (!method_exists($migration, $mthd))
					{
						$this->output->error('Sorry, modules do not currently support the ' . $mthd . ' method');
					}

					$migration->$mthd($name, $position);
				}
				else
				{
					$mthd = $method . 'Module' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

					if (!method_exists($migration, $mthd))
					{
						$this->output->error('Sorry, modules do not currently support the ' . $mthd . ' method');
					}

					$migration->$mthd($name);
				}
				break;

			case 'tpl':
				$mthd = $method . 'Template' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, templates do not currently support the ' . $mthd . ' method');
				}

				$element = $name = substr($name, 4);
				$name    = ucwords($name);
				$client  = $this->arguments->getOpt('client', 'site');

				if ($method == 'delete')
				{
					$migration->$mthd($element, (($client == 'admin') ? 1 : 0));
				}
				else
				{
					$migration->$mthd($element, $name, (($client == 'admin') ? 1 : 0));
				}
				break;

			case 'plg':
				preg_match('/plg_([[:alnum:]]+)_([[:alnum:]]*)/', $name, $matches);

				if (!isset($matches[1]) || !isset($matches[2]))
				{
					$this->output->error('This does not appear to be a valid extension name.');
				}

				$folder  = $matches[1];
				$element = $matches[2];
				$mthd    = $method . 'Plugin' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, plugins do not currently support the ' . $mthd . ' method');
				}

				$migration->$mthd($folder, $element);
				break;

			default:
				$this->output->error('This does not appear to be a valid extension name.');
			break;
		}

		$this->output->addLine("Successfully {$method}" . ((substr($method, -1) == 'e') ? 'd' : 'ed') . " {$name}!", 'success');
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