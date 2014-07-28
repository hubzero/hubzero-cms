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

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Scaffolding class for components
 **/
class Component extends Scaffolding
{
	/**
	 * Construct new component
	 *
	 * @return void
	 **/
	public function construct()
	{
		// Get component name from user input
		$name = null;
		if ($this->arguments->getOpt('n') || $this->arguments->getOpt('name') || $this->arguments->getOpt(4))
		{
			// Set name, according to priority of inputs
			$name = ($this->arguments->getOpt(4)) ? $this->arguments->getOpt(4) : $name;
			$name = ($this->arguments->getOpt('n')) ? $this->arguments->getOpt('n') : $name;
			$name = ($this->arguments->getOpt('name')) ? $this->arguments->getOpt('name') : $name;
			$name = strtolower($name);
		}
		else
		{
			// If name wasn't provided, and we're in interactive mode...ask for it
			if ($this->output->isInteractive())
			{
				$name = $this->output->getResponse('What do you want the component name to be?');
			}
			else
			{
				$this->output->error("Error: a component name should be provided.");
			}
		}

		// Define our install directory or get it from args
		$install_dir = JPATH_ROOT . DS . 'components';
		if ($this->arguments->getOpt('install-dir') && strlen(($this->arguments->getOpt('install-dir'))) > 0)
		{
			$install_dir = JPATH_ROOT . DS . trim($this->arguments->getOpt('install-dir'), DS) . DS . 'components';
		}

		if (substr($name, 0, 3) == 'com')
		{
			$name = substr($name, 4);
		}

		// Make sure component doesn't already exist
		if (is_dir($install_dir . DS . 'com_' . $name))
		{
			$this->output->error("Error: the component name provided ({$name}) seems to already exists.");
		}

		// Make component
		$this->addTemplateFile("{$this->getType()}.tmpl", $install_dir . DS . 'com_' . $name)
			 ->addReplacement('component_name', $name)
			 ->make();
	}

	/**
	 * Help doc for component scaffolding class
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Create a new component.'
			)
			->addArgument(
				'-n, --name: component name',
				'Give the component name. The component name can also be provided
				as the next word following the command as shown here:
				"muse scaffolding create component awesome"',
				'Example: -n=awesome, --name=awesomer'
			)
			->addArgument(
				'--install-dir: installation directory',
				'Directory in which the component should be installed. Can be helpful
				when installing a component in some sort of subsite or alternate
				configuration. Scaffolding with use JPATH_ROOT as the default.',
				'Example: --install-dir=site/groups/1987'
			);
	}
}