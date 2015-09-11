<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Console output service provider
 */
class OutputServiceProvider extends Middleware
{
	/**
	 * Register the service provider
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['output'] = function($app)
		{
			return new \Hubzero\Console\Output();
		};
	}

	/**
	 * Handle request in stack
	 * 
	 * @param   object  $request  Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		$arguments = $this->app['arguments'];
		$output    = $this->app['output'];

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
		$this->app->forget('output');
		$this->app->set('output', $output);

		return $response;
	}
}