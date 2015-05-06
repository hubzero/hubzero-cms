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
	 * @return void
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
				$path .= 'libraries' . DS . 'Hubzero' . DS . implode(DS, $parts) . DS;

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
	 * @return void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Scaffolding for PHPUnit tests'
			);
	}
}