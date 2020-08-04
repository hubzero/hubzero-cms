<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Configuration;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Aliases configuration class for adding command aliases
 **/
class Aliases extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just call help
	 *
	 * @return  void
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
	 * @return  void
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
		App::get('client')->call('configuration', 'set', $this->arguments, $this->output);
	}

	/**
	 * Shows help text for aliases command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Add and remove user-specific command line aliases.');
	}
}
