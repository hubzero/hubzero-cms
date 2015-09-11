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

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

/**
 * Scaffolding class for muse commands
 *
 * @museIgnoreHelp
 **/
class Command extends Scaffolding
{
	/**
	 * Construct new command
	 *
	 * @return  void
	 **/
	public function construct()
	{
		// Get command name from user input
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
				$name = $this->output->getResponse('What do you want the command name to be?');
			}
			else
			{
				$this->output->error("Error: a command name should be provided.");
			}
		}

		// Define our install directory or get it from args
		$dest = PATH_CORE . DS . 'libraries' . DS . 'Hubzero' . DS . 'Console' . DS . 'Command' . DS . ucfirst($name) . '.php';

		// Make command
		$this->addTemplateFile("{$this->getType()}.tmpl", $dest)
			 ->addReplacement('command_name', $name)
			 ->make();
	}

	/**
	 * Help doc for command scaffolding class
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Create a new console command.'
			)
			->addArgument(
				'-n, --name: command name',
				'Give the command name. The command name can also be provided
				as the next word following the command as shown here:
				"muse scaffolding create command awesome"',
				'Example: -n=awesome, --name=awesomer'
			);
	}
}