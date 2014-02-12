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
 * Scaffolding class for generating template extensions
 **/
class Scaffolding implements CommandInterface
{
	/**
	 * Output object, implements the Output interface
	 *
	 * @var object
	 **/
	protected $output;

	/**
	 * Arguments object, implements the Argument interface
	 *
	 * @var object
	 **/
	protected $arguments;

	/**
	 * Array of vars to replace in template
	 *
	 * @var array
	 **/
	private $replacements = array();

	/**
	 * Array of template files to parse
	 *
	 * @var array
	 **/
	private $templateFiles = array();

	/**
	 * The type of scaffolding item that we're making
	 *
	 * @var string
	 **/
	private $type = false;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		$this->output    = $output;
		$this->arguments = $arguments;
		$this->type      = $this->arguments->getOpt(3);
	}

	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for scaffolding command
	 *
	 * @return void
	 **/
	public function help()
	{
		if ($this->type)
		{
			$class = __NAMESPACE__ . '\\Scaffolding\\' . ucfirst($this->type);
			$obj   = new $class($this->output, $this->arguments);

			// Call the help method
			$obj->help();
		}
		else
		{
			$this->output->addLine('Scaffolding help doc...coming soon');
		}
	}

	/**
	 * Create a new item from scaffolding templates
	 *
	 * @return void
	 **/
	public function create()
	{
		$class = __NAMESPACE__ . '\\Scaffolding\\' . ucfirst($this->type);

		if (class_exists($class))
		{
			$obj = new $class($this->output, $this->arguments);
		}
		else
		{
			if (empty($this->type))
			{
				$this->output->error('Error: Sorry, scaffolding can\'t create nothing/everything. Try telling it what you want to create.');
			}
			else
			{
				$this->output->error('Error: Sorry, scaffolding doesn\'t know how to create a ' . $this->type);
			}
		}

		// Call the construct method
		$obj->construct();
	}

	/**
	 * Get the type of template we're making
	 *
	 * @return (string) $type
	 **/
	protected function getType()
	{
		return $this->type;
	}

	/**
	 * Make template
	 *
	 * @return void
	 **/
	protected function make()
	{
		if (count($this->templateFiles) > 0)
		{
			foreach ($this->templateFiles as $template)
			{
				if (!copy($template['path'], $template['destination']))
				{
					$this->output->error("Error: an problem occured copying {$template['path']} to {$template['destination']}.");
				}

				// Get template contents
				$contents = file_get_contents($template['path']);
				$contents = $this->doReplacements($contents);

				// Write file
				file_put_contents($template['destination'], $contents);
			}
		}
	}

	/**
	 * Make replacements in a given content string
	 *
	 * @param  (string) $contents - incoming content
	 * @return (string) $contents - outgoing content
	 **/
	private function doReplacements($contents)
	{
		// Replace variables
		if (isset($this->replacements) && count($this->replacements) > 0)
		{
			foreach ($this->replacements as $k => $v)
			{
				if (is_array($v))
				{
					foreach ($v as $replacement)
					{
						foreach ($replacement as $key => $value)
						{
							$contents = preg_replace("/%={$key}=%/", $value, $contents, 1);
						}
					}
				}
				else
				{
					$contents = str_replace("%={$k}=%", $v, $contents);
				}
			}
		}

		// Now parse for nested templates
		preg_match_all('/\$\^[[:alpha:]\.]*\^\$/', $contents, $matches, PREG_SET_ORDER);

		if (isset($matches) && count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				foreach ($match as $key)
				{
					// Get template contents
					$keyReal = preg_replace('/\$\^([[:alpha:]\.]*)\^\$/', '$1', $key);
					$subPath = __DIR__ . DS . 'Scaffolding' . DS . 'Templates' . DS . $this->getType() . '.' . $keyReal . '.tmpl';

					if (!is_file($subPath))
					{
						continue;
					}

					$subTmpl = file_get_contents($subPath);

					if (isset($this->replacements[$keyReal]) && is_array($this->replacements[$keyReal]))
					{
						$count    = count($this->replacements[$keyReal]);
						$repeat   = str_repeat($key, $count);
						$contents = str_replace($key, $repeat, $contents);
					}

					$contents = str_replace($key, $subTmpl, $contents);
					$contents = $this->doReplacements($contents);
				}
			}
		}

		return $contents;
	}

	/**
	 * Add a new replacement for the template
	 *
	 * @param  (string) $key
	 * @param  (string) $value
	 * @return (object) $this - for method chaining
	 **/
	protected function addReplacement($key, $value)
	{
		$this->replacements[$key] = $value;

		return $this;
	}

	/**
	 * Add a new template file
	 *
	 * @param  (string) $filename - template filename
	 * @param  (string) $destination - final location of template file after making
	 * @return (object) $this - for method chaining
	 **/
	protected function addTemplateFile($filename, $destination)
	{
		$this->templateFiles[] = array(
			'path'        => __DIR__ . DS . 'Scaffolding' . DS . 'Templates' . DS . $filename,
			'destination' => $destination
		);

		return $this;
	}
}