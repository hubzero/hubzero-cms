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
		$group  = \Hubzero\User\Group::getInstance($cn);

		// make sure we have all the needed stuff
		if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
		{
			// get com_groups params to get upload path
			$uploadPath      = $this->filespace($group);
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

				// if we have a build route functions, run it
				if (function_exists($parseRouteFunction))
				{
					// get current route and remove prefix
					$currentRoute = rtrim(Request::path(), '/');
					$currentRoute = trim(str_replace('groups/' . $group->get('cn') . '/' . $active, '', $currentRoute), '/');

					// split route into segements
					$segments = explode('/', $currentRoute);

					// run segments through parser
					$vars = $parseRouteFunction($segments);

					// set each var
					foreach ($vars as $key => $var)
					{
						Request::setVar($key, $var);
					}
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
	 * Method that fires after an SEF route is built
	 *
	 * @param   object  $uri  URI after route has been built
	 * @return  void
	 */
	public function onAfterBuildSefRoute($uri)
	{
		// get the current segments
		$currentSegments = explode('/', trim(Request::path(), '/'));

		// make sure were building within groups
		if (!isset($currentSegments[0]) || !isset($currentSegments[1]) || $currentSegments[0] != 'groups')
		{
			return;
		}

		// get option from uri
		$url         = $uri->toString();
		$url         = str_replace('index.php', '', $url);
		$urlSegments = explode('/', trim($url, '/'));

		// make sure this is not a group route.
		if (!isset($urlSegments[0]) || $urlSegments[0] == 'groups')
		{
			return;
		}

		// get query string
		$query = $uri->getQuery(true);

		// get request options
		$cn     = Request::getVar('cn', '');
		$active = Request::getVar('active', '');

		// load group object
		$group  = \Hubzero\User\Group::getInstance($cn);

		// make sure we have all the needed stuff
		if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
		{
			// get com_groups params to get upload path
			$uploadPath      = $this->filespace($group);
			$componentPath   = $uploadPath . DS . 'components';
			$componentRouter = $componentPath . DS . 'com_' . $active . DS . 'router.php';

			// make sure uri is a super group component
			if (!is_dir($componentPath . DS . 'com_' . $urlSegments[0]))
			{
				return;
			}

			// if we have a router
			if (file_exists($componentRouter))
			{
				// include router
				require_once $componentRouter;

				// build function name
				$buildRouteFunction = ucfirst($active) . 'BuildRoute';
				$buildRouteFunction = str_replace(array('-', '.'), '', $buildRouteFunction);

				// if we have a build route functions, run it
				if (function_exists($buildRouteFunction))
				{
					// get segments from router
					$routeParts = $buildRouteFunction($query);

					// build result
					$routeResult = implode('/', $routeParts);
					$routeResult = DS . 'groups' . DS . $group->get('cn') . DS . $active . DS . $routeResult;

					// set the new uri path and query string
					$uri->setPath($routeResult);
					$uri->setQuery($query);
				}
			}
		}
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