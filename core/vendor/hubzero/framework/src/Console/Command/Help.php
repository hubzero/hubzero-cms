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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

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
	 * @return  void
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
	 * @return  void
	 **/
	public function help()
	{
		$this->execute();
	}

	/**
	 * Parse the commands directory in search of available commands
	 *
	 * @return  void
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
	 * @param   string  $root  The root directory for commands
	 * @param   string  $dir   The current directory to search for commands (relative to root)
	 * @return  void
	 **/
	private function getCommands($root = null, $dir = null)
	{
		$root = $root ?: __DIR__;
		$dir  = $dir  ?: '';
		$cur  = $root . ((!empty($dir)) ? DS . $dir : '');

		// Get files from command directory to use in list
		$files = array_diff(scandir($cur), array('..', '.'));

		foreach ($files as $file)
		{
			if (is_file($cur . DS . $file) && strpos($file, '.php') !== false)
			{
				$namespace  = str_replace($root . DS, '', $cur . DS . $file);
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
			else if (is_dir($cur . DS . $file))
			{
				$this->getCommands($root, ((!empty($dir)) ? $dir . DS : '') . $file);
			}
		}
	}

	/**
	 * Merge flat list of commands into dimensional array
	 *
	 * @param   array  $commands  The commands to parse
	 * @return  array
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
	 * @param   mixed   $entry  Item(s) to output
	 * @param   int     $ind    Indentation level
	 * @param   string  $path   Path to current entry
	 * @return  void
	 **/
	private function addEntries($entry, $ind = 1, $path = '')
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