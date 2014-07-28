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
 * Help class for rendering utility-wide help documentation
 **/
class Configure extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * Sets/updates config vars, creating .muse config file as needed
	 *
	 * @return void
	 **/
	public function execute()
	{
		$options = $this->arguments->getOpts();

		if (empty($options))
		{
			if ($this->output->isInteractive())
			{
				$options = array();
				$option  = $this->output->getResponse('What do you want to configure [name|email|etc...] ?');

				if (is_string($option) && !empty($option))
				{
					$options[$option] = $this->output->getResponse("What do you want your {$option} to be?");
				}
				else if (empty($option))
				{
					$this->output->error("Please specify what option you want to set.");
				}
				else
				{
					$this->output->error("The {$option} option is not currently supported.");
				}
			}
			else
			{
				$this->output = $this->output->getHelpOutput();
				$this->help();
				$this->output->render();
				return;
			}
		}

		if ($this->save($options))
		{
			$this->output->addLine('Saved new configuration!', 'success');
		}
		else
		{
			$this->output->error('Failed to save configuration');
		}
	}

	/**
	 * Show help text for configure command
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Store shared configuration variables used by the command line tool.
				These will, for example, be used to fill in docblock stubs when
				using the scaffolding command.'
			)
			->addArgument(
				'--name',
				'Set your preferred name.',
				'Example: --name="John Doe"'
			)
			->addArgument(
				'--email',
				'Set your preferred email address.',
				'Example: --name=john.doe@gmail.com'
			);
	}

	/**
	 * Save config vars
	 *
	 * @param  (array) $config - array of vars to save
	 * @return void
	 **/
	private function save($config)
	{
		if (count($config) == 0)
		{
			return true;
		}

		$home = getenv('HOME');
		$path = $home . DS . '.muse';

		// If configuration file already exists, combine incoming with current
		if (is_file($path))
		{
			$currentConfig = parse_ini_file($path);
			$config = array_merge($currentConfig, $config);
		}

		// Bulid ini format file contents
		$contents = '';
		foreach ($config as $k => $v)
		{
			$contents .= "{$k} = $v\n";
		}

		// Put contents
		if (file_put_contents($path, $contents) !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}