<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Request;
use Route;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'middleware.php';

/**
 * Controller class for tools (default)
 */
class Zones extends SiteController
{
	/**
	 * Page Not found
	 *
	 * @return  exit
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
	 * @param   string   $path
	 * @param   boolean  $isFile
	 * @return  mixed
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
	 * @return  exit
	 */
	public function assetsTask()
	{
		$file = Request::getString('file');
		$file = self::normalize_path('/' . trim($file, '/'), true);

		if (empty($file))
		{
			return $this->notFoundTask();
		}

		$zone = new \Components\Tools\Models\Middleware\Zone(Request::getInt('id', 0));
		if (!$zone->exists())
		{
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
