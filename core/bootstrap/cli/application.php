<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Hubzero\Console\Event;
use Hubzero\Console\Config;
use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;

/**
 * CLI application class
 */
class JCli extends JApplication
{
	/**
	 * Cli application constructor
	 *
	 * @param  array $config an optional associative array of configuration settings
	 * @return void
	 */
	public function __construct($config=[])
	{
		$config['clientId'] = 6;

		parent::__construct($config);
	}

	/**
	 * Initialise the application
	 *
	 * @param  array $options an optional associative array of configuration settings.
	 * @return void
	 */
	public function initialise($options=[])
	{
		$arguments = App::get('arguments');
		$output    = App::get('output');

		try
		{
			$arguments->parse();
		}
		catch (UnsupportedCommandException $e)
		{
			$output->error($e->getMessage());
		}
		catch (UnsupportedTaskException $e)
		{
			$output->error($e->getMessage());
		}

		// Check for interactivity flag and set on output accordingly
		if ($arguments->getOpt('non-interactive'))
		{
			$output->makeNonInteractive();
		}

		// Check for color flag and set on output accordingly
		if ($arguments->getOpt('no-colors'))
		{
			$output->makeUnColored();
		}

		// If task is help, set the output to our output class with extra methods for rendering help doc
		if ($arguments->get('task') == 'help')
		{
			$output = $output->getHelpOutput();
		}

		// If the format opt is present, try to use the appropriate output subclass
		if ($arguments->getOpt('format'))
		{
			$output = $output->getOutputFormatter($arguments->getOpt('format'));
		}

		// Register any user specific events
		if ($hooks = Config::get('hooks'))
		{
			foreach ($hooks as $trigger => $scripts)
			{
				foreach ($scripts as $script)
				{
					Event::register($trigger, function() use ($script, $output)
					{
						if ($output->getMode() != 'minimal')
						{
							$output->addLine("Running '{$script}'");
						}
						shell_exec(escapeshellcmd($script));
					});
				}
			}
		}

		// Reset the output stored on the application
		App::forget('output');
		App::set('output', $output);
	}

	/**
	 * Route the application
	 *
	 * @return  void
	 */
	public function route()
	{
		// Nothing to route
	}

	/**
	 * Dispatch the application
	 *
	 * @param  string $component the component to dispatch (does not apply here)
	 * @return void
	 */
	public function dispatch($component=null)
	{
		$class = App::get('arguments')->get('class');
		$task  = App::get('arguments')->get('task');

		$command   = new $class(App::get('output'), App::get('arguments'));
		$shortName = strtolower(with(new \ReflectionClass($command))->getShortName());

		// Fire default before event
		Event::fire($shortName . '.' . 'before' . ucfirst($task));

		$command->{$task}();

		// Fire default after event
		Event::fire($shortName . '.' . 'after' . ucfirst($task));
	}

	/**
	 * Render the application
	 *
	 * @return void
	 */
	public function render()
	{
		App::get('output')->render();
	}
}
