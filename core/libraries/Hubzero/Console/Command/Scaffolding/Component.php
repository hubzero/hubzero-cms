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
 * Scaffolding class for components
 *
 * @museIgnoreHelp
 **/
class Component extends Scaffolding
{
	/**
	 * Construct new component
	 *
	 * @return  void
	 **/
	public function construct()
	{
		// Get component name from user input
		$name = null;
		if ($this->arguments->getOpt('n') || $this->arguments->getOpt('name') || $this->arguments->getOpt(4))
		{
			// Set name, according to priority of inputs
			$name = ($this->arguments->getOpt(4)) ? $this->arguments->getOpt(4) : $name;
			$name = ($this->arguments->getOpt('n')) ? $this->arguments->getOpt('n') : $name;
			$name = ($this->arguments->getOpt('name')) ? $this->arguments->getOpt('name') : $name;
			$name = strtolower($name);
		}
		else
		{
			// If name wasn't provided, and we're in interactive mode...ask for it
			if ($this->output->isInteractive())
			{
				$name = $this->output->getResponse('What do you want the component name to be?');
			}
			else
			{
				$this->output->error("Error: a component name should be provided.");
			}
		}

		// Define our install directory or get it from args
		$install_dir = PATH_CORE . DS . 'components';
		if ($this->arguments->getOpt('install-dir') && strlen(($this->arguments->getOpt('install-dir'))) > 0)
		{
			// @FIXME: need to be able to distinguish between path_app and path_core here
			$install_dir = PATH_CORE . DS . trim($this->arguments->getOpt('install-dir'), DS) . DS . 'components';
		}

		if (substr($name, 0, 3) == 'com')
		{
			$name = substr($name, 4);
		}

		// Make sure component doesn't already exist
		if (is_dir($install_dir . DS . 'com_' . $name))
		{
			$this->output->error("Error: the component name provided ({$name}) seems to already exists.");
		}

		// Make component
		$this->addTemplateFile("{$this->getType()}.tmpl", $install_dir . DS . 'com_' . $name)
		     ->addReplacement('component_name', $name)
		     ->addReplacement('option', 'com_' . $name)
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
				'Create a new component.'
			)
			->addArgument(
				'-n, --name: component name',
				'Give the component name. The component name can also be provided
				as the next word following the command as shown here:
				"muse scaffolding create component awesome"',
				'Example: -n=awesome, --name=awesomer'
			)
			->addArgument(
				'--install-dir: installation directory',
				'Directory in which the component should be installed. Can be helpful
				when installing a component in some sort of subsite or alternate
				configuration. Scaffolding with use PATH_CORE as the default.',
				'Example: --install-dir=site/groups/1987'
			);
	}
}