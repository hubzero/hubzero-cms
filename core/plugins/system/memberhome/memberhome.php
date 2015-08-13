<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   HUBzero
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * Redirect to Member Home Page
 */
class plgSystemMemberHome extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @return  boolean
	 */
	public function onAfterRoute()
	{
		$task = Request::getVar('task', 'none');

		if (User::isGuest() || !App::isSite() || $task == 'user.logout')
		{
			return false;
		}

		/*$ignoredURLs = (string) $this->params->get('ignore_urls', '');

		if ($ignoredURLs)
		{
			$ignoredURLArray = explode("\r\n",$ignoredURLs);

			$fullURL = Request::current();

			foreach ($ignoredURLArray as $str)
			{
				$pos = strpos($fullURL, $str);
				if ($pos !== false)
				{
					return false;
				}
			}
		}

		$ignoredOptions = (string) $this->params->get('ignore_options', '');

		if ($ignoredOptions)
		{
			$option = Request::getCmd('option', '');
			$ignoredOptionsArray = explode("\r\n", $ignoredOptions);

			foreach ($ignoredOptionsArray as $str)
			{
				if ($str == $option)
				{
					return false;
				}
			}
		}*/

		$menuId = $this->params->get('menuId', 0);

		if (!$menuId)
		{
			return false;
		}

		$menu = App::get('menu');
		$activeMenu  = $menu->getActive();
		$defaultMenu = $menu->getDefault();

		if ($activeMenu == $defaultMenu)
		{
			$menu->setActive($menuId);
			$item = $menu->getItem($menuId);
			$vars = $item->query;

			foreach ($vars as $key => $var)
			{
				Request::setVar($key, $var);
			}

			//Request::set($vars, 'method', true);
			//App::redirect(Route::url('index.php?Itemid=' . $menuId, false));
		}
		return true;
	}
}
