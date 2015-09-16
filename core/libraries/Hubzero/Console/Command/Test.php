<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$path  = PATH_CORE . DS;
		$parts = explode('_', $extension);
		switch ($parts[0])
		{
			case 'lib':
				unset($parts[0]);
				$path .= 'libraries' . DS . 'Hubzero' . DS . ucfirst(implode(DS, $parts)) . DS . 'Tests';
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
			while ($c = fgetc($pipes[1])) print $c;
			while ($s = fgets($pipes[1])) print $s;
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
		$base        = PATH_CORE . DS . 'libraries' . DS . 'Hubzero';
		$directories = array_diff(scandir($base), ['.', '..']);

		$tests = [];

		foreach ($directories as $directory)
		{
			if (is_dir($base . DS . $directory . DS . 'Tests'))
			{
				$tests[] = $directory;
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
				$name = 'lib_' . strtolower($test);
				$this->output->addLine($name, 'success');
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
				will search the provided extension for a directory titled "Test".  The
				command will parse the provided extension, and expects a name in the format
				of com_name, mod_name, plg_folder_element, or lib_name.  Libraries are
				assumed to be in the Hubzero library folder.',
				'',
				true
			);
	}
}