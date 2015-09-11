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
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Content\Server;
use Request;
use App;
use Config;

require_once __DIR__ . DS . 'base.php';

/**
 * API controller for the time component
 */
class formv1_0 extends base
{
	/**
	 * Gets form images
	 *
	 * @apiMethod GET
	 * @apiUri    /courses/form/image
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Form ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "form_version",
	 * 		"description": "Form version number",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "file",
	 * 		"description": "Image filename",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "token",
	 * 		"description": "Session authentication token",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function imageTask()
	{
		$id       = Request::getInt('id', 0);
		$version  = Request::getInt('form_version', 0);
		$filename = Request::getVar('file', '');
		$filename = urldecode($filename);
		$filename = PATH_APP . DS . 'site' . DS . 'courses' . DS . 'forms' . DS . $id . DS . (($version) ? $version . DS : '') . ltrim($filename, DS);

		// Ensure the file exist
		if (!file_exists($filename))
		{
			// Return message
			App::abort(404, 'Image not found');
		}

		// Add silly simple security check
		$token      = Request::getString('token', false);
		$session_id = App::get('session')->getId();
		$secret     = Config::get('secret');
		$hash       = hash('sha256', $session_id . ':' . $secret);

		if ($token !== $hash)
		{
			App::abort(401, 'You don\'t have permission to do this');
		}

		// Initiate a new content server and serve up the file
		header("HTTP/1.1 200 OK");
		$xserver = new Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false);

		if (!$xserver->serve())
		{
			// Return message
			App::abort(500, 'Failed to serve the image');
		}
	}
}