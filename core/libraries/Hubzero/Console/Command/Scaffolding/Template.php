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
 * Scaffolding class for templates
 *
 * @museIgnoreHelp
 **/
class Template extends Scaffolding
{
	/**
	 * Copy and rename template
	 *
	 * @return  void
	 **/
	public function doCopy()
	{
		$from = null;
		$to   = null;

		if ($this->arguments->getOpt(4) && $this->arguments->getOpt(5) && $this->arguments->getOpt(6))
		{
			if ($this->arguments->getOpt(5) == 'to')
			{
				$from = strtolower($this->arguments->getOpt(4));
				$to   = strtolower($this->arguments->getOpt(6));
			}
			else if ($this->arguments->getOpt(5) == 'from')
			{
				$to   = strtolower($this->arguments->getOpt(4));
				$from = strtolower($this->arguments->getOpt(6));
			}
		}
		else
		{
			// If name wasn't provided, and we're in interactive mode...ask for it
			if ($this->output->isInteractive())
			{
				$from = $this->output->getResponse('What template to you want to use as the source?');
				$to   = $this->output->getResponse('What do you want to call the new template?');
			}
			else
			{
				$this->output->error("Error: please provide the source template and destination template name");
			}
		}

		// Make sure template doesn't already exist
		if (is_dir(PATH_APP . DS . 'templates' . DS . $to))
		{
			$this->output->error("Error: the template destination alread exists.");
		}
		if (!is_dir(PATH_APP . DS . 'templates' . DS . $from))
		{
			$this->output->error("Error: the template source does not appear to exist.");
		}

		// Make component
		$this->addTemplateFile(PATH_APP . DS . 'templates' . DS . $from, PATH_APP . DS . 'templates' . DS . $to, true)
			 ->addTemplateFile(PATH_APP . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $from . '.ini', PATH_APP . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $to . '.ini', true)
			 ->addReplacement(strtoupper($from), strtoupper($to))
			 ->addReplacement(ucfirst($from), ucfirst($to))
			 ->addReplacement($from, $to)
			 ->doBlindReplacements()
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
				'Scaffolding for templates'
			);
	}
}