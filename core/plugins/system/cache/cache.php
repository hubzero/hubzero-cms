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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * Page Cache Plugin
 */
class plgSystemCache extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @var  object
	 */
	private $id = null;

	/**
	 * Converting the site URL to fit to the HTTP request
	 *
	 * @return  void
	 */
	public function getId()
	{
		if (!$this->id)
		{
			$this->id = 'page.' . md5($_SERVER['REQUEST_URI']);
		}
		return $this->id;
	}

	/**
	 * Converting the site URL to fit to the HTTP request
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		if (App::isAdmin() || Config::get('debug'))
		{
			return;
		}

		if (Notify::any() || !App::has('cache'))
		{
			return;
		}

		if (User::isGuest() && Request::method() == 'GET' && $this->params->get('pagecache', false))
		{
			$id = $this->getId();

			if ($data = App::get('cache')->get($id))
			{
				App::get('response')->setContent($data);

				App::get('response')->compress(App::get('config')->get('gzip', false));

				if ($this->params->get('browsercache', false))
				{
					App::get('response')->headers->set('HTTP/1.x 304 Not Modified', true);
				}

				App::get('response')->headers->set('ETag', $id);

				App::get('response')->send();

				if ($profiler = App::get('profiler'))
				{
					$profiler->mark('afterCache');
					echo implode('', $profiler->marks());
				}

				App::close();
			}
		}
	}

	/**
	 * Save cached data
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
		if (App::isAdmin() || Config::get('debug'))
		{
			return;
		}

		if (Notify::any() || !App::has('cache'))
		{
			return;
		}

		if (User::isGuest() && $this->params->get('pagecache', false))
		{
			// We need to check again here, because auto-login plugins
			// have not been fired before the first aid check
			App::get('cache')->put(
				$this->getId(),
				App::get('response')->getContent(),
				App::get('config')->get('lifetime', 45)
			);
		}
	}

	/**
	 * Clean out cached CSS files
	 *
	 * @param   string   $group
	 * @param   integer  $client_id
	 * @return  void
	 */
	public function onCleanCache($group = null, $client_id = 0)
	{
		$dir = PATH_APP . DS . 'cache';

		if (!is_dir($dir))
		{
			return;
		}

		$paths = array(
			$dir . '/site/site.css',
			$dir . '/site/site.less.cache'
		);

		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				Filesystem::delete($path);
			}
		}
	}
}
