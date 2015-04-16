<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Test class
 **/
class Test extends Base implements CommandInterface
{
	/**
	 * Default execute method
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->run();
	}

	/**
	 * Run the tests
	 *
	 * @return void
	 **/
	public function run()
	{
		// Make sure phpunit is installed
		exec('which phpunit', $output);
		if (!$output)
		{
			$this->output->error('PHPUnit does not appear to be installed');
		}

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
		$cmd = 'phpunit --no-globals-backup --bootstrap ' . PATH_CORE . DS . 'cli' . DS . 'shim.php ' . escapeshellarg($path) . ' 2>&1';

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
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'A custom PHPUnit testing wrapper. This helps with setting up the 
				environment and allowing for specialized options related to testing.'
			)
			->addArgument(
				'extension',
				'The first option to the "run" command should be a specific extension.
				Currently, running the entire suite of tests is not allowed.  The command
				will search the provided extension for a directory titled "Test".  The
				command will parse the provided extension, and expects a name in the format
				of com_name, mod_name, plg_folder_element, or lib_name.  Libraries are
				assumed to be in the Hubzero library folder.'
			);
	}
}