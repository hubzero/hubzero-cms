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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2014-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Request;
use Route;
use Lang;
use User;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'middleware.php');

/**
 * Controller class for tools (default)
 */
class Zones extends SiteController
{
	/**
	 * Page Not found
	 *
	 * @return    exit
	 */
	public function notFoundTask()
	{
		ob_end_clean();
		header("HTTP/1.1 404 Not Found");
		echo "Not Found";
		ob_end_flush();
		exit;
	}

	/**
	 * Normalize a path
	 *
	 * @param     string  $path
	 * @param     boolean $isFile
	 * @return    mixed
	 */
	private static function normalize_path($path, $isFile = false)
	{
		if (!isset($path[0]) || $path[0] != '/')
		{
			return false;
		}

		$parts = explode('/', $path);

		$result = array();

		foreach ($parts as $part)
		{
			if ($part === '' || $part == '.')
			{
				continue;
			}

			if ($part == '..')
			{
				array_pop($result);
			}
			else
			{
				$result[] = $part;
			}
		}

		if ($isFile) // Files can't end with directory separator or special directory names
		{
			if ($part == '' || $part == '.' || $part == '..')
			{
				return false;
			}
		}

		return '/' . implode('/', $result) . ($isFile ? '' : '/');
	}

	/**
	 * Asset delivery function.
	 *
	 * @return    exit
	 */
	public function assetsTask()
	{
		$file = Request::getVar('file');
		$file = self::normalize_path('/' . trim($file, '/'), true);

		if (empty($file))
		{
			echo 'file:' . $file; die();
			return $this->notFoundTask();
		}

		$zone = new \Components\Tools\Models\Middleware\Zone(Request::getInt('id', 0));
		if (!$zone->exists())
		{
			echo 'zone: ' . $zone->get('id'); die();
			return $this->notFoundTask();
		}

		$file = $zone->logo('path') . '/' . ltrim($file, '/');

		if (!is_file($file) || !is_readable($file))
		{
			return $this->notFoundTask();
		}

		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			App::abort(404, Lang::txt('COM_TOOLS_SERVER_ERROR'));
		}

		exit;
	}
}

