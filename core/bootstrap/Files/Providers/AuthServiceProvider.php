<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
			header('HTTP/1.1 403 Forbidden');
			print "<h2>Forbidden</h2>Your request is missing credentials or has bad credentials.";
			exit();
		}

		$this->app['session']   = $session_id;
		$this->app['moderator'] = $moderator;

		return $response;
	}
}
