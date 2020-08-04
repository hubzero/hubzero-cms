<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Test class
 **/
class Test extends Base implements CommandInterface
{
	/**
	 * Default execute method
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->run();
	}

	/**
	 * Run the tests
	 *
	 * @museDescription  Runs available tests for the given extension
	 *
	 * @return  void
	 **/
	public function run()
	{
		// Get the extension to test...for now, this is required
		if (!$extension = $this->arguments->getOpt(3))
		{
			$this->output->error('Please provide a specific extension to test');
		}

		// Parse the extension and build a real path
		$core  = dirname(dirname(__DIR__));
		$path  = 'core';
		if (strstr($extension, ':'))
		{
			$blocks = explode(':', $extension);
			$path   = array_shift($blocks);
			$extension = implode('', $blocks);
		}
		$parts = explode('_', $extension);
		switch ($parts[0])
		{
			case 'tpl':
				unset($parts[0]);
				$path = ($path == 'app' ? PATH_APP : PATH_CORE) . DS . 'templates' . DS . implode('_', $parts) . DS . 'tests';
				break;

			case 'plg':
				unset($parts[0]);
				$path = ($path == 'app' ? PATH_APP : PATH_CORE) . DS . 'plugins' . DS . implode(DS, $parts) . DS . 'tests';
				break;

			case 'mod':
				$path = ($path == 'app' ? PATH_APP : PATH_CORE) . DS . 'modules' . DS . $extension . DS . 'tests';
				break;

			case 'com':
				$path = ($path == 'app' ? PATH_APP : PATH_CORE) . DS . 'components' . DS . $extension . DS . 'tests';
				break;

			case 'lib':
				unset($parts[0]);
				$path = $core . DS . ucfirst(implode(DS, $parts)) . DS . 'Tests';
				break;

			default:
				$this->output->error('Sorry, we were not able to find an extension by that name or that extension type is not currently supported');
				break;
		}

		// Make sure the test directory exists
		if (!is_dir($path))
		{
			$this->output->error('Sorry, we could\'t find a test directory for that extension');
		}

		// Build the command
		$cmd = 'php ' . PATH_CORE . DS . 'bin' . DS . 'phpunit --no-globals-backup --bootstrap ' . PATH_CORE . DS . 'bootstrap' . DS . 'test' . DS . 'start.php ' . escapeshellarg($path) . ' 2>&1';

		// We want to stream the output, so set up what we need to do that
		$descriptorspec = [
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		];

		$process = proc_open($cmd, $descriptorspec, $pipes);

		if (is_resource($process))
		{
			while (false !== ($c = fgetc($pipes[1])))
			{
				print $c;
			}
			while (false !== ($s = fgets($pipes[1])))
			{
				print $s;
			}
		}

		// Close process
		proc_close($process);
	}

	/**
	 * Lists the test suites available to run
	 *
	 * @museDescription  Shows a list of extensions with available tests
	 *
	 * @return  void
	 **/
	public function show()
	{
		$tests = [];

		$nodes = array(
			['lib', dirname(dirname(__DIR__))],
			['core', PATH_CORE . DS . 'templates'],
			['app', PATH_APP . DS . 'templates'],
			['core', PATH_CORE . DS . 'components'],
			['app', PATH_APP . DS . 'components'],
			['core', PATH_CORE . DS . 'modules'],
			['app', PATH_APP . DS . 'modules']
		);

		foreach ($nodes as $node)
		{
			$key  = $node[0];
			$base = $node[1];

			$directories = array_diff(scandir($base), ['.', '..']);

			foreach ($directories as $directory)
			{
				if (is_dir($base . DS . $directory . DS . 'Tests')
				 || is_dir($base . DS . $directory . DS . 'tests'))
				{
					if (basename($base) == 'templates')
					{
						$directory = 'tpl_' . $directory;
					}
					$tests[] = $key . ($key == 'lib' ? '_' : ':') . strtolower($directory);
				}
			}
		}

		// Plugins have one extra level of directories
		$nodes = array(
			['core', PATH_CORE . DS . 'plugins'],
			['app', PATH_APP . DS . 'plugins']
		);

		foreach ($nodes as $node)
		{
			$key  = $node[0];
			$base = $node[1];

			$directories = array_diff(scandir($base), ['.', '..']);

			foreach ($directories as $directory)
			{
				if (!is_dir($base . DS . $directory))
				{
					continue;
				}

				$subdirectories = array_diff(scandir($base . DS . $directory), ['.', '..']);

				foreach ($subdirectories as $subdirectory)
				{
					if (is_dir($base . DS . $directory . DS . $subdirectory . DS . 'Tests')
					 || is_dir($base . DS . $directory . DS . $subdirectory . DS . 'tests'))
					{
						$tests[] = $key . ($key == 'lib' ? '_' : ':') . 'plg_' . strtolower($directory) . '_' . strtolower($subdirectory);
					}
				}
			}
		}

		if (!count($tests))
		{
			$this->output->addLine('There are currently no tests suites available to be run.', 'warning');
		}
		else
		{
			foreach ($tests as $test)
			{
				$this->output->addLine($test, 'success');
			}
		}
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
				'A custom PHPUnit testing wrapper. This helps with setting up the
				environment and allowing for specialized options related to testing.'
			)
			->addTasks($this)
			->addArgument(
				'extension',
				'The first option to the "run" command should be a specific extension.
				Currently, running the entire suite of tests is not allowed.  The command
				will search the provided extension for a directory titled "Tests".  The
				command will parse the provided extension, and expects a name in the format
				of com_name, mod_name, plg_folder_element, or lib_name.  Prepend "app:" or
				"core:" to designate the specific root directory corresponding to ROOT/app
				and ROOT/core respectively. Libraries are assumed to be in the core Hubzero
				library folder.',
				'',
				true
			);
	}
}
