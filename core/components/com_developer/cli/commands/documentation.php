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

/**
 * Developer documentation command class
 **/
class Documentation extends Base implements CommandInterface
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
	 * Generate documentation for API
	 * 
	 * @return void
	 */
	public function generate()
	{
		// Generate documentation
		$generator     = new \Hubzero\Api\Doc\Generator();
		$documentation = $generator->output('array', true);

		// Output error messages
		foreach ($documentation['errors'] as $error)
		{
			$this->output->addLine($error, 'error');
		}

		// Successfully processed the following files
		foreach ($documentation['files'] as $file)
		{
			$this->output->addLine('Successfully processed the file: ' . $file, 'success');
		}
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
				'Api documentation related commands.'
			);
	}
}
