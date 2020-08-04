<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

/**
 * Scaffolding class for unit tests
 *
 * @museIgnoreHelp
 **/
class Test extends Scaffolding
{
	/**
	 * Construct new test
	 *
	 * @return  void
	 **/
	public function construct()
	{
		// Extension
		$extension = null;
		if ($this->arguments->getOpt('e') || $this->arguments->getOpt('extension') || $this->arguments->getOpt(4))
		{
			// Set extension, according to priority of inputs
			$extension = ($this->arguments->getOpt(4)) ? $this->arguments->getOpt(4) : $extension;
			$extension = ($this->arguments->getOpt('e')) ? $this->arguments->getOpt('e') : $extension;
			$extension = ($this->arguments->getOpt('extension')) ? $this->arguments->getOpt('extension') : $extension;
			$extension = strtolower($extension);
		}
		else
		{
			// If extension wasn't provided, and we're in interactive mode...ask for it
			if ($this->output->isInteractive())
			{
				$extension = $this->output->getResponse('What extension do you the test to pertain to?');
			}
			else
			{
				$this->output->error("Error: an extension should be provided.");
			}
		}

		// Parse the extension and build a real path
		$path  = PATH_CORE . DS;
		$parts = explode('_', $extension);
		switch ($parts[0])
		{
			case 'lib':
				unset($parts[0]);
				// Hubzero\Console\Command\Scaffolding = __DIR__
				// Hubzero\Console\Command             = dirname(__DIR__)
				// Hubzero\Console                     = dirname(dirname(__DIR__))
				// Hubzero                             = dirname(dirname(dirname(__DIR__)))
				$path = dirname(dirname(dirname(__DIR__))) . DS . implode(DS, $parts) . DS;

				$this->addReplacement('namespace', 'Hubzero\\' . ucfirst($parts[1]) . '\\Tests');
				break;

			default:
				$this->output->error('Sorry, that extension type is not currently supported');
				break;
		}

		// Make sure the extension exists
		if (!is_dir($path))
		{
			$this->output->error('Sorry, we couldn\'t find an extension by that name');
		}

		// Add tests dir to path and create it if it's not there
		$path .= 'Tests';
		if (!is_dir($path))
		{
			mkdir($path);
		}

		// Type of test
		$type = strtolower($this->arguments->getOpt('type', 'basic'));

		if (!in_array($type, ['basic', 'database']))
		{
			$this->output->error('Sorry, test type should be one of either "basic" or "database"');
		}

		// Make test
		$this->addTemplateFile("{$this->getType()}.{$type}.tmpl", $path . DS . 'Example' . ucfirst($type) . 'Test.php')
			 ->make();
	}

	/**
	 * Help doc for component scaffolding class
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Scaffolding for PHPUnit tests'
			);
	}
}
