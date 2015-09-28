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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Content\Migration\Base as Migration;

/**
 * Extension class
 **/
class Extension extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		if ($this->output->isInteractive())
		{
			$actions = array('add', 'delete', 'install', 'enable', 'disable');
			$action  = $this->output->getResponse('What do you want to do? ['.implode("|", $actions).']');

			if (in_array($action, $actions))
			{
				$this->$action();
			}
			else
			{
				$this->output->error('Sorry, I don\'t know how to do that.');
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

	/**
	 * Add an entry to the extension table
	 *
	 * @museDescription  Adds a new extension to the extensions table
	 *
	 * @return  void
	 **/
	public function add()
	{
		$this->alter('add');
	}

	/**
	 * Delete an entry from the extension table
	 *
	 * @museDescription  Deletes an existing entry from the extensions table
	 *
	 * @return  void
	 **/
	public function delete()
	{
		$this->alter('delete');
	}

	/**
	 * Install an extension
	 *
	 * @museDescription  Installs an extension, adding it if it hasn't been already
	 *
	 * @return  void
	 **/
	public function install()
	{
		$this->alter('install');
	}

	/**
	 * Enable an extension
	 *
	 * @museDescription  Enables an existing extension
	 *
	 * @return  void
	 **/
	public function enable()
	{
		$this->alter('enable');
	}

	/**
	 * Disable an extension
	 *
	 * @museDescription  Disables an existing extension
	 *
	 * @return  void
	 **/
	public function disable()
	{
		$this->alter('disable');
	}

	/**
	 * Alter extension
	 *
	 * @param   string  $method  The method name
	 * @return  void
	 **/
	private function alter($method)
	{
		$migration = new Migration(App::get('db'));

		$name = null;
		if ($this->arguments->getOpt('name'))
		{
			$name = $this->arguments->getOpt('name');
		}

		if (!isset($name))
		{
			if ($this->output->isInteractive())
			{
				$name = $this->output->getResponse("What extension were you wanting to {$method}?");
			}
			else
			{
				$this->output = $this->output->getHelpOutput();
				$this->help();
				$this->output->render();
				return;
			}
		}

		$extensionType = substr($name, 0, 3);

		switch ($extensionType)
		{
			case 'com':
				if ($method == 'add' || $method == 'delete')
				{
					$extensionName = ucfirst(substr($name, 4));
					$mthd          = $method . 'ComponentEntry';
				}
				else
				{
					$extensionName = $name;
					$mthd          = $method . 'Component';
				}

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, components do not currently support the ' . $mthd . ' method');
				}

				$migration->$mthd($extensionName);
				break;

			case 'mod':
				$client = $this->arguments->getOpt('client', 'site');

				if ($method == 'install')
				{
					$mthd     = $method . 'Module';
					$position = $this->arguments->getOpt('position', null);

					if (!isset($position))
					{
						if ($this->output->isInteractive())
						{
							$position = $this->output->getResponse("Where should the module be positioned?");
						}
						else
						{
							$this->output->addLine('Please provide a position for the module', 'warning');
							return;
						}
					}

					if (!method_exists($migration, $mthd))
					{
						$this->output->error('Sorry, modules do not currently support the ' . $mthd . ' method');
					}

					$migration->$mthd(str_replace('mod_', '', $name), $position, true, '', (($client == 'admin') ? 1 : 0));
				}
				else
				{
					$mthd = $method . 'Module' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

					if (!method_exists($migration, $mthd))
					{
						$this->output->error('Sorry, modules do not currently support the ' . $mthd . ' method');
					}

					$migration->$mthd($name, 1, '', (($client == 'admin') ? 1 : 0));
				}
				break;

			case 'tpl':
				$mthd = $method . 'Template' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, templates do not currently support the ' . $mthd . ' method');
				}

				$element = $name = substr($name, 4);
				$name    = ucwords($name);
				$client  = $this->arguments->getOpt('client', 'site');
				$isCore  = $this->arguments->getOpt('core', false) ? 1 : 0;

				if ($method == 'delete')
				{
					$migration->$mthd($element, (($client == 'admin') ? 1 : 0));
				}
				else if ($method == 'add')
				{
					$migration->$mthd($element, $name, (($client == 'admin') ? 1 : 0), 1, 0, null, $isCore);
				}
				else
				{
					$migration->$mthd($element, $name, (($client == 'admin') ? 1 : 0), null, $isCore);
				}
				break;

			case 'plg':
				preg_match('/plg_([[:alnum:]]+)_([[:alnum:]]*)/', $name, $matches);

				if (!isset($matches[1]) || !isset($matches[2]))
				{
					$this->output->error('This does not appear to be a valid extension name.');
				}

				$folder  = $matches[1];
				$element = $matches[2];
				$mthd    = $method . 'Plugin' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

				if (!method_exists($migration, $mthd))
				{
					$this->output->error('Sorry, plugins do not currently support the ' . $mthd . ' method');
				}

				$migration->$mthd($folder, $element);
				break;

			default:
				$this->output->error('This does not appear to be a valid extension name.');
			break;
		}

		$this->output->addLine("Successfully {$method}" . ((substr($method, -1) == 'e') ? 'd' : 'ed') . " {$name}!", 'success');
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
				'Extension management utility functions.'
			)
			->addTasks($this);
	}
}