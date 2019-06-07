<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Files\Providers;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Content\Moderator;

/**
 * Token based authentication service provider
 */
class AuthServiceProvider extends Middleware
{
	/**
	 * Handle request in stack
	 * 
	 * @param   object  $request  Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		// Get the referer to parse for the applicable app
		$referer = $request->header('referer');
		$app     = Request::create($referer)->segment(1, 'site');
		$app     = (in_array($app, ['site', 'administrator'])) ? $app : 'site';

		// Get secret and session name manually
		$secret      = $this->app['config']->get('secret');
		$cookie_name = md5(md5($secret . $app));
		$session_id  = $request->getVar($cookie_name, false, 'COOKIE');

		// Build moderator
		$identifier = $request->segment(2);
		$moderator  = new Moderator($identifier, $session_id, $secret);

		if (!$moderator->validateToken())
		{
			header('HTTP/1.1 401 You don\'t have permission to do this');
			exit();
		}

		$this->app['session']   = $session_id;
		$this->app['moderator'] = $moderator;

		return $response;
	}
}
