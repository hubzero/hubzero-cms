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
 *
 * @museIgnoreHelp
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
		$this->output
			->addLine(
				'Muse: HUBzero Command Line Utility',
				array(
					'color'  => 'blue',
					'format' => 'underline'
			))
			->addSpacer()
			->addString('Usage: muse ')
			->addString('[command]', array('color'=>'green'))
			->addString('[:namespace] ', array('color'=>'blue'))
			->addString('[task] ', array('color'=>'yellow'))
			->addString('[options]')
			->addSpacer()
			->addSpacer()
			->addLine('Commands');

		// Process commands
		$this->processCommands();

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
	 * Parse the commands directory in search of available commands
	 *
	 * @return void
	 **/
	private function processCommands()
	{
		// Get all valid commands
		$this->getCommands();
		$commands = $this->_commands;

		// Sort commands
		sort($commands);

		// Build dimensional representation of commands
		$commands = $this->mergeCommands($commands);

		// Now output commands
		$this->addEntries($commands);
	}

	/**
	 * Helper to get files in commands directy. This is used to generate a list of commands.
	 *
	 * @param  (string) $dir - directory to search for commands
	 * @return void
	 **/
	private function getCommands($dir=null)
	{
		$dir = (isset($dir)) ? $dir : __DIR__;

		// Get files from command directory to use in list
		$files = array_diff(scandir($dir), array('..', '.'));

		foreach ($files as $file)
		{
			if (is_file($dir . DS . $file) && strpos($file, '.php') !== false)
			{
				$npath      = str_replace('\\', DS, __NAMESPACE__);
				$namespace  = str_replace(JPATH_ROOT . DS . 'libraries' . DS . $npath . DS, '', $dir . DS . $file);
				$namespace  = str_replace(DS, '\\', $namespace);
				$class      = str_replace('.php', '', $namespace);

				// Make sure a valid class exists
				if (class_exists(__NAMESPACE__ . '\\' . $class))
				{
					$reflection = new \ReflectionClass(__NAMESPACE__ . '\\' . $class);

					// Make sure it implements the Command Interface
					if ($reflection->implementsInterface(__NAMESPACE__ . '\CommandInterface'))
					{
						$comment = $reflection->getDocComment();

						// Check for help ignore flag
						if (!preg_match('/@museIgnoreHelp/', $comment))
						{
							$this->_commands[] = $class;
						}
					}
				}
			}
			else if (is_dir($dir . DS . $file))
			{
				$this->getCommands($dir . DS . $file);
			}
		}
	}

	/**
	 * Merge flat list of commands into dimensional array
	 *
	 * @param  (array) $commands - commands to parse
	 * @return (array) $parsed   - parsed commands
	 **/
	private function mergeCommands($commands)
	{
		$parsed = array();

		// Loop through commands
		foreach ($commands as $command)
		{
			$bits = array();

			// Break up namespaced commands
			if (strpos($command, '\\'))
			{
				$bits    = explode('\\', $command);
				$command = $bits[count($bits)-1];
				unset($bits[count($bits)-1]);
			}

			$aux =& $parsed;

			// Loop through bits and build path to element we want to set
			foreach ($bits as $b)
			{
				$aux =& $aux[$b];
			}

			// Set element
			$aux[] = $command;
		}

		return $parsed;
	}

	/**
	 * Output command entries
	 *
	 * @param  (mixed)  $entry - item(s) to output
	 * @param  (int)    $ind   - indentation level
	 * @param  (string) $path  - path to current entry
	 * @return void
	 **/
	private function addEntries($entry, $ind=1, $path='')
	{
		// If it's an array, loop through it
		if (is_array($entry))
		{
			foreach ($entry as $element => $entry)
			{
				// If this is just another directory, output an element
				if (is_string($element) && !empty($path))
				{
					$this->output->addLine(
						$element,
						array(
							'color'       => 'blue',
							'indentation' => $ind+2
						)
					);
				}

				// Dive deeper - if element is string, add it to our path
				$this->addEntries($entry, $ind+2, $path . ((is_string($element)) ? "\\{$element}" : ''));
			}
		}
		else
		{
			$this->output->addLine(
				$entry,
				array(
					'color'       => (($ind > 3) ? 'blue' : 'green'),
					'indentation' => $ind
				)
			);

			// Increment indentation
			$ind += 2;

			// Get this command's methods
			$reflection = new \ReflectionClass(__NAMESPACE__ . $path . '\\' . $entry);
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
							'indentation' => $ind
						)
					);
				}
			}
		}
	}
}