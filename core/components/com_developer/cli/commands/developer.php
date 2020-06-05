<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Cli\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Developer command class
 **/
class Developer extends Base implements CommandInterface
{
	/**
	 * Default (required) command
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
	 * Revokes all oauth related tokens/codes
	 * 
	 * @return  void
	 */
	public function revoke()
	{
		App::get('client')->call('developer:accesstokens', 'revoke', $this->arguments, $this->output);
		App::get('client')->call('developer:refreshtokens', 'revoke', $this->arguments, $this->output);
		App::get('client')->call('developer:authorizationcodes', 'revoke', $this->arguments, $this->output);
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Api developer related commands.'
			);
	}
}
