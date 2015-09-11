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