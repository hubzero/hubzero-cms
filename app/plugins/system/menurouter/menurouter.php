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
defined('_HZEXEC_') or die();

/**
 * Manipulate routing rules
 */
class plgSystemMenurouter extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to catch the onAfterRoute event.
	 *
	 * @return  boolean
	 */
	public function onBeforeRoute()
	{
		if (!App::isSite())
		{
			return false;
		}

		// Append a build rule
		// This is called whenever Route::url() is used
		$router = App::get('router');
		$router->rules('build')->append('menurouter', function ($uri)
		{
			$route = $uri->getPath();
			$route = trim($route, '/');
			$segments = explode('/', $route);

			$link = 'index.php?Itemid=';
			$active = false;
			// Community menu
			if (isset($segments[0]) && in_array($segments[0], array('groups', 'projects', 'members', 'partners')))
			{
				// The appropriate method here might be to look
				// up the menu item's parent menu item and use its
				// alias/path
				// $menu = App::get('menu');

				// foreach ($menu->getItems('menutype', 'default') as $m) {
					// echo '<pre>' . var_dump($menu->getItems('id', $m->parent_id)) . '</pre><br>';
					// echo '<pre>' . var_dump($m) . '</pre><br>';
				// }

				array_unshift($segments, 'community');

				// Again, here we would ideally use the parent
				// menu item's info
				$name = 'Community';

				$active = true;
			}

			// Resources menu
			if (isset($segments[0]) && in_array($segments[0], array('publications', 'collections')))
			{
				array_unshift($segments, 'qubesresources');
				$name = 'Resources';
				$active = true;
			}

			// News menu
			if (isset($segments[0]) && in_array($segments[0], array('blog', 'newsletter', 'events')))
			{
				array_unshift($segments, 'news');
				$name = 'News & Activities';
				$active = true;
			}

			// About menu
			if (isset($segments[0]) && in_array($segments[0], array('citations', 'usage')))
			{
				array_unshift($segments, 'about');
				$name = 'About';
				$active = true;
			}

			if ($active)
			{
				$found = false;
				$items = App::get('pathway')->items();
				foreach ($items as $item)
				{
					if ($item->link == $link && $item->name == $name)
					{
						$found = true;
					}
				}
				// Currently injects an extra unnecessary Community in breadcrumbs.
				// I think this is because of redundancy with the System -> Subnav
				// plugin.  So, in other words, this is unnecessary!!!
				// if (!$found)
				// {
				// 	App::get('pathway')->prepend(
				// 		$name,
				// 		$link
				// 	);
				// }

				$result = implode('/', $segments);
				$route  = ($result != '') ? '/' . $result : '';

				$uri->setPath($route);
			}

			return $uri;
		});

		// Check the request for a URL missing
		// the desired prefix. Fix, and redirect.
		$request = App::get('request');

		// NOTE: We only want to do this if the
		// request method is GET.
		if ($request->method() != 'GET')
		{
			return false;
		}

		$segment = $request->segment(1);

		// Community menu
		if (in_array($segment, array('groups', 'projects', 'members', 'partners')))
		{
			$uri = str_replace('/' . $segment, '/community/' . $segment, $request->current(true));
			App::redirect($uri);
		}

		// Resources menu
		if (in_array($segment, array('publications', 'collections')))
		{
			$uri = str_replace('/' . $segment, '/qubesresources/' . $segment, $request->current(true));
			App::redirect($uri);
		}

		// News menu
		if (in_array($segment, array('blog', 'newsletter', 'events')))
		{
			$uri = str_replace('/' . $segment, '/news/' . $segment, $request->current(true));
			App::redirect($uri);
		}

		// About menu
		if (in_array($segment, array('citations', 'usage')))
		{
			$uri = str_replace('/' . $segment, '/about/' . $segment, $request->current(true));
			App::redirect($uri);
		}

		return true;
	}
}
