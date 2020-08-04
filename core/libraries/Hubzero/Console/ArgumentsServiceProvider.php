<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Console\Arguments;
use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;
use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Console arguments service provider
 */
class ArgumentsServiceProvider extends Middleware
{
	/**
	 * Register the service provider
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['arguments'] = function($app)
		{
			global $argv;

			// Register namespace for App commands and component commands
			if (defined('PATH_APP'))
			{
				Arguments::registerNamespace('\App\Commands', [
					PATH_APP . '/commands',
				]);
				Arguments::registerNamespace('\Components\{$1}\Commands', [
					PATH_APP . '/components/com_{$1}/commands',
					PATH_CORE . '/components/com_{$1}/commands'
				]);
			}

			return new Arguments($argv);
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

		try
		{
			$this->app->get('arguments')->parse();
		}
		catch (UnsupportedCommandException $e)
		{
			$this->app->get('output')->error($e->getMessage());
		}
		catch (UnsupportedTaskException $e)
		{
			$this->app->get('output')->error($e->getMessage());
		}

		return $response;
	}
}
