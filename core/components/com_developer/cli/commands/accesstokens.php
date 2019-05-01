<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Cli\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Database\Query;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Developer access tokens command class
 **/
class Accesstokens extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Delete all access tokens
	 *
	 * @return void
	 **/
	public function revoke()
	{
		// Attempt to delete tokens
		try
		{
			with(new Query)->delete('#__developer_access_tokens')->execute();
		}
		catch (QueryFailedException $e)
		{
			$this->output->error('Error:' . $e->getMessage());
		}

		// Successfully deleted tokens
		$this->output->addLine('All access tokens successfully revoked.', 'success');
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Api access token related commands.'
			);
	}
}
