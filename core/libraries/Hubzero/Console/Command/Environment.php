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
 * Environment class
 **/
class Environment extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return  void
	 **/
	public function execute()
	{
		// Note that we're using both the muse config and the global config repositories
		$this->output->addLine('Current user     : ' . Config::get('user_name') . ' <' . Config::get('user_email') . '>');
		$this->output->addLine('Current database : ' . \Config::get('db'));
	}

	/**
	 * Help output
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Environment display/management functions');
	}
}
