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