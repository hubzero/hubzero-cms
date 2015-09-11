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

namespace Hubzero\Console\Command\Configuration;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Aliases configuration class for adding command aliases
 **/
class Aliases extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just call help
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
	 * Adds a new console alias
	 *
	 * @return  void
	 **/
	public function add()
	{
		// Get the alias we're setting
		$name = $this->arguments->getOpt(3);
		$path = $this->arguments->getOpt(4);

		// Delete the primary args so they aren't added as top level config values
		$this->arguments->deleteOpt(3);
		$this->arguments->deleteOpt(4);

		// Set the new aliases argument
		$this->arguments->setOpt('aliases', array($name => $path));

		// Redirect back to the basic configuration set method
		App::get('client')->call('configuration', 'set', $this->arguments, $this->output);
	}

	/**
	 * Shows help text for aliases command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Add and remove user-specific command line aliases.');
	}
}