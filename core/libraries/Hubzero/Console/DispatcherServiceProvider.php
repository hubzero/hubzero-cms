<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Console dispatcher service provider
 */
class DispatcherServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
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

		$class = $this->app->get('arguments')->get('class');
		$task  = $this->app->get('arguments')->get('task');

		$command   = new $class($this->app->get('output'), $this->app->get('arguments'));
		$shortName = strtolower(with(new \ReflectionClass($command))->getShortName());

		// Fire default before event
		Event::fire($shortName . '.' . 'before' . ucfirst($task));

		$command->{$task}();

		// Fire default after event
		Event::fire($shortName . '.' . 'after' . ucfirst($task));

		$this->app->get('output')->render();

		return $response;
	}
}
