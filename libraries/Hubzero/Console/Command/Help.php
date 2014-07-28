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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Help class for rendering utility-wide help documentation
 **/
class Help extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return void
	 **/
	public function execute()
	{
		$commands = $this->getCommands();

		$this->output
			->addLine(
				'Muse: HUBzero Command Line Utility',
				array(
					'color'  => 'blue',
					'format' => 'underline'
			))
			->addSpacer()
			->addString('Usage: muse ')
			->addString('[command] ', array('color'=>'green'))
			->addString('[task] ', array('color'=>'yellow'))
			->addString('[options]')
			->addSpacer()
			->addSpacer()
			->addLine('Commands');

		foreach ($commands as $command)
		{
			$this->output->addLine(
				$command,
				array(
					'color'       => 'green',
					'indentation' => 3
				)
			);

			$reflection = new \ReflectionClass(__NAMESPACE__ . '\\' . $command);
			$methods    = $reflection->getMethods();

			foreach ($methods as $method)
			{
				// We're assuming here that all public methods are available to be called
				if ($method->isPublic() && !$method->isConstructor() && $method->name != 'execute' && $method->name != 'help')
				{
					$this->output->addLine(
						$method->name,
						array(
							'color'       => 'yellow',
							'indentation' => 5
						)
					);
				}
			}
		}

		$this->output
			->addSpacer()
			->addLine('Type "muse [command] help" to view command level options')
			->addSpacer();
	}

	/**
	 * Just call execute. Normally this would output our help text, but because this is the
	 * help command, we don't need a separate help method.
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->execute();
	}

	/**
	 * Helper to get files in commands directy. This is used to generate a list of commands.
	 *
	 * @return (array) $commands
	 **/
	private function getCommands()
	{
		// Get files from command directory to use in list
		$files    = array_diff(scandir(__DIR__), array('..', '.', 'CommandInterface.php', 'Base.php'));
		$commands = array();

		foreach ($files as $file)
		{
			if (is_file(__DIR__ . DS . $file))
			{
				$commands[] = str_replace('.php', '', $file);
			}
		}

		return $commands;
	}
}