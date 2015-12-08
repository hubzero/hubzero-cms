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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Files;

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

		return $response;
	}
}
