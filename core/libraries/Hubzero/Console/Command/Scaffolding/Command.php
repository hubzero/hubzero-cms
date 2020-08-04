<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
