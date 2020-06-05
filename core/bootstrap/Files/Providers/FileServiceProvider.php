<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Files\Providers;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Content\Server;

/**
 * File service provider
 */
class FileServiceProvider extends Middleware
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

		$filename = $this->app['moderator']->getPath();

		// Ensure the file exist
		if (!file_exists($filename))
		{
			// Return message
			header('HTTP/1.1 404 Not found');
			exit();
		}

		// Initiate a new content server
		$server = new Server();
		$server->disposition('inline');
		$server->acceptranges(true);
		$server->allowXsendFile();
		$server->filename($filename);

		// Serve up the file
		$result = $server->serve();

		// If the file was served and we have an event dispatcher...
		if ($result && $this->app->has('dispatcher'))
		{
			$dt = new \Hubzero\Utility\Date('now');

			$this->app['dispatcher']->trigger('onFileServe', [
				'file'       => $filename,
				'timestamp'  => $dt->format('Y-m-d H:i:s'),
				'session_id' => $this->app->has('session') ? $this->app['session'] : '???'
			]);
		}

		return $response;
	}
}
