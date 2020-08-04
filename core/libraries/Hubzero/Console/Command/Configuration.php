<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Config;

/**
 * Help class for rendering utility-wide help documentation
 **/
class Configuration extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->set();
	}

	/**
	 * Sets a configuration option
	 *
	 * Sets/updates config vars, creating .muse config file as needed
	 *
	 * @museDescription  Sets the defined key/value pair and saves it into the user's configuration
	 *
	 * @return  void
	 **/
	public function set()
	{
		$options = $this->arguments->getOpts();

		if (empty($options))
		{
			if ($this->output->isInteractive())
			{
				$options = array();
				$option  = $this->output->getResponse('What do you want to configure [name|email|etc...] ?');

				if (is_string($option) && !empty($option))
				{
					$options[$option] = $this->output->getResponse("What do you want your {$option} to be?");
				}
				else if (empty($option))
				{
					$this->output->error("Please specify what option you want to set.");
				}
				else
				{
					$this->output->error("The {$option} option is not currently supported.");
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

		if (Config::save($options))
		{
			$this->output->addLine('Saved new configuration!', 'success');
		}
		else
		{
			$this->output->error('Failed to save configuration');
		}
	}

	/**
	 * Shows help text for configure command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Store shared configuration variables used by the command line tool.
				These will, for example, be used to fill in docblock stubs when
				using the scaffolding command.'
			)
			->addTasks($this)
			->addArgument(
				'--{keyName}',
				'Sets the variable keyName to the given value.',
				'Example: --name="John Doe"'
			);
	}
}
