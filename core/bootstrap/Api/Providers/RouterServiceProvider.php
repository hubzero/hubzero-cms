<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Api\Providers;

use Hubzero\Base\Middleware;
use Hubzero\Http\RedirectResponse;
use Hubzero\Http\Request;
use Hubzero\Routing\Manager;

/**
 * Router service provider
 */
class RouterServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['router'] = function($app)
		{
			return new Manager($app, array(PATH_CORE, PATH_APP));
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
		if (!$this->app->runningInConsole())
		{
			$this->app['dispatcher']->trigger('system.onBeforeRoute');

			foreach ($this->app['router']->parse($request->getUri()) as $key => $val)
			{
				$request->setVar($key, $val, 'get');
			}

			$this->app['dispatcher']->trigger('system.onAfterRoute');

			if ($this->app->has('profiler'))
			{
				$this->app['profiler'] ? $this->app['profiler']->mark('afterRoute') : null;
			}
		}

		return $this->next($request);
	}
}
