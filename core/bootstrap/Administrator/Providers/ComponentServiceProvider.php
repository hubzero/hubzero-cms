<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Component\Loader;
use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Component loader service provider
 */
class ComponentServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['component'] = function($app)
		{
			return new Loader($app);
		};
	}

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		if (!$this->app->runningInConsole())
		{
			$component = $request->getCmd('option');
			if (!$component)
			{
				$this->app->abort(404);
			}

			$contents = $this->app['component']->render($component);

			$response->setContent($contents);

			$this->app['dispatcher']->trigger('system.onAfterDispatch');

			if ($this->app->has('profiler'))
			{
				$this->app['profiler'] ? $this->app['profiler']->mark('afterDispatch') : null;
			}
		}

		return $response;
	}
}
