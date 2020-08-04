<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\App;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Utility\Composer;

/**
 * Repository class for adding and removing composer package repositories
 **/
class Repository extends Base implements CommandInterface
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
	 * Show packages
	 *
	 * @museDescription Shows a list of active repositories
	 *
	 * @return  void
	 **/
	public function show()
	{
		$repositories = Composer::getRepositoryConfigs();
		$this->output->addRawFromAssocArray($repositories);
	}

	/**
	 * Add a repository
	 *
	 * @museDescription Adds a repository
	 *
	 * @return  void
	 **/
	public function add()
	{
		//Add via composer.json for now
	}

	/**
	 * Remove a repository
	 *
	 * @museDescription Removes a repository
	 *
	 * @return  void
	 **/
	public function remove()
	{
		//Remove via composer.json for now
	}

	/**
	 * Shows help text for repository command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Add, remove, and update repositories for packages')
			->addTasks($this);
	}
}
