<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

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
	 * Force SSL if site is configured to and
	 * the connection is not secure.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if ($this->app['config']->get('force_ssl') >= 1)
		{
			if (!$this->app['request']->isSecure())
			{
				$uri = str_replace('http:', 'https:', $this->app['request']->getUri());

				$redirect = new RedirectResponse($uri);
				$redirect->setRequest($this->app['request']);
				$redirect->send();

				$this->app->close();
			}
		}
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
