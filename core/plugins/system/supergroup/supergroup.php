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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for hubzero
 */
class plgSystemSupergroup extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method that fires after before a super group displays a super group comonent
	 *
	 * @return  void
	 */
	public function onBeforeRenderSuperGroupComponent()
	{
		// get request options
		$option = Request::getCmd('option', '');

		// make sure we in groups
		if ($option != 'com_groups')
		{
			return;
		}

		$cn     = Request::getVar('cn', '');
		$active = Request::getVar('active', '');

		// load group object
		$group  = Hubzero\User\Group::getInstance($cn);

		// make sure we have all the needed stuff
		if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
		{
			// get com_groups params to get upload path
			//$uploadPath      = $this->filespace($group);
			$params = App::get('component')->params('com_groups');
			$uploadPath      = PATH_APP . DS . trim($params->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
			$componentPath   = $uploadPath . DS . 'components';
			$componentRouter = $componentPath . DS . 'com_' . $active . DS . 'router.php';

			// if we have a router
			if (file_exists($componentRouter))
			{
				// include router
				require_once $componentRouter;

				// build function name
				$parseRouteFunction = ucfirst($active) . 'ParseRoute';
				$parseRouteFunction = str_replace(array('-', '.'), '', $parseRouteFunction);

				$name = '\\Components\\' . ucfirst($active) . '\\Site\\Router';
				$alt  = '\\Components\\' . ucfirst($active) . '\\Router';

				// get current route and remove prefix
				$currentRoute = rtrim(Request::path(), '/');
				$currentRoute = trim(str_replace('groups/' . $group->get('cn') . '/' . $active, '', $currentRoute), '/');

				// split route into segements
				$segments = explode('/', $currentRoute);
				$vars = array();

				if (class_exists($name))
				{
					$router = new $name;

					// get segments from router
					$vars = $router->parse($segments);
				}
				else if (class_exists($alt))
				{
					$router = new $alt;

					// get segments from router
					$vars = $router->parse($segments);
				}
				// if we have a build route functions, run it
				if (function_exists($parseRouteFunction))
				{
					// run segments through parser
					$vars = $parseRouteFunction($segments);
				}

				// set each var
				foreach ($vars as $key => $var)
				{
					Request::setVar($key, $var);
				}
			}

			// remove "sg_" prefix for super group query params
			foreach (Request::query() as $k => $v)
			{
				if (strpos($k, 'sg_') !== false)
				{
					Request::setVar(str_replace('sg_', '', $k), $v);
				}
			}
		}
	}

	/**
	 * Push a new routing rule after the CMS has been initialized
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		App::get('router')->rules('build')->append('supergroup', function ($uri)
		{
			// get the current segments
			$currentSegments = explode('/', trim(\App::get('request')->path(), '/'));

			// make sure were building within groups
			if (!isset($currentSegments[0]) || !isset($currentSegments[1]) || $currentSegments[0] != 'groups')
			{
				return $uri;
			}

			// get option from uri
			$url         = $uri->toString();
			$url         = str_replace('index.php', '', $url);
			$urlSegments = explode('/', trim($url, '/'));

			// make sure this is not a group route.
			if (!isset($urlSegments[0]) || $urlSegments[0] == 'groups')
			{
				return $uri;
			}

			// get request options
			$cn     = \App::get('request')->getVar('cn', '');
			$active = \App::get('request')->getVar('active', '');

			// load group object
			$group  = \Hubzero\User\Group::getInstance($cn);

			// make sure we have all the needed stuff
			if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
			{
				$params = \App::get('component')->params('com_groups');

				// get com_groups params to get upload path
				$uploadPath      = PATH_APP . DS . trim($params->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber'); //$this->filespace($group);
				$componentPath   = $uploadPath . DS . 'components';
				$componentRouter = $componentPath . DS . 'com_' . $active . DS . 'router.php';

				// make sure uri is a super group component
				if (!is_dir($componentPath . DS . 'com_' . $urlSegments[0]))
				{
					return $uri;
				}

				// if we have a router
				if (file_exists($componentRouter))
				{
					// include router
					require_once $componentRouter;

					// build function name
					$buildRouteFunction = ucfirst($active) . 'BuildRoute';
					$buildRouteFunction = str_replace(array('-', '.'), '', $buildRouteFunction);

					$name = '\\Components\\' . ucfirst($active) . '\\Site\\Router';
					$alt  = '\\Components\\' . ucfirst($active) . '\\Router';

					$uri->parse($uri->uri());

					// get query string
					$query = $uri->getQuery(true);

					$routeParts = array();

					if (class_exists($name))
					{
						$router = new $name;

						// get segments from router
						$routeParts = $router->build($query);
					}
					else if (class_exists($alt))
					{
						$router = new $alt;

						// get segments from router
						$routeParts = $router->build($query);
					}
					// if we have a build route functions, run it
					else if (function_exists($buildRouteFunction))
					{
						// get segments from router
						$routeParts = $buildRouteFunction($query);
					}

					// build result
					$routeResult = '/groups/' . $group->get('cn') . '/' . $active . '/' . implode('/', $routeParts);

					// set the new uri path and query string
					$uri->setPath($routeResult);
					$uri->setQuery($query);
					$uri->delVar('option');
				}
			}

			return $uri;
		});
	}

	/**
	 * Get the file upload path for a group
	 *
	 * @param   object  $group
	 * @return  string
	 */
	protected function filespace($group)
	{
		$params = Component::params('com_groups');
		return PATH_APP . DS . trim($params->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
	}
}