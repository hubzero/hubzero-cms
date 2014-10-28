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

namespace Hubzero\Console\Command\Configuration;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Application;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Aliases configuration class for adding command aliases
 **/
class Aliases extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just call help
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
	 * Adds a new console alias
	 *
	 * @return void
	 **/
	public function add()
	{
		// Get the alias we're setting
		$name = $this->arguments->getOpt(3);
		$path = $this->arguments->getOpt(4);

		// Delete the primary args so they aren't added as top level config values
		$this->arguments->deleteOpt(3);
		$this->arguments->deleteOpt(4);

		// Set the new aliases argument
		$this->arguments->setOpt('aliases', array($name => $path));

		// Redirect back to the basic configuration set method
		Application::call('configuration', 'set', $this->arguments, $this->output);
	}

	/**
	 * Shows help text for aliases command
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output->addOverview('Add and remove user-specific command line aliases.');
	}
}