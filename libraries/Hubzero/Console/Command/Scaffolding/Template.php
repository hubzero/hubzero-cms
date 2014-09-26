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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
	 * @return void
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
		if (is_dir(JPATH_ROOT . DS . 'templates' . DS . $to))
		{
			$this->output->error("Error: the template destination alread exists.");
		}
		if (!is_dir(JPATH_ROOT . DS . 'templates' . DS . $from))
		{
			$this->output->error("Error: the template source does not appear to exist.");
		}

		// Make component
		$this->addTemplateFile(JPATH_ROOT . DS . 'templates' . DS . $from, JPATH_ROOT . DS . 'templates' . DS . $to, true)
			 ->addTemplateFile(JPATH_ROOT . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $from . '.ini', JPATH_ROOT . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $to . '.ini', true)
			 ->addReplacement(strtoupper($from), strtoupper($to))
			 ->addReplacement(ucfirst($from), ucfirst($to))
			 ->addReplacement($from, $to)
			 ->doBlindReplacements()
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
				'Scaffolding for templates'
			);
	}
}