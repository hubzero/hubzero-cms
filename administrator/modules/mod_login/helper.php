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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Login;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Request;
use Lang;
use Html;

/**
 * Module class for displaying a login form
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!\App::isAdmin())
		{
			return;
		}

		$return  = self::getReturnURI();
		$freturn = base64_encode('index.php?' . Request::getQueryString());

		$returnQueryString = (!empty($return)) ? "&return={$return}" : '';
		$authenticators    = [];
		$plugins           = \Plugin::byType('authentication');

		foreach ($plugins as $p)
		{
			$pparams = new Registry($p->params);

			// Make sure it supports admin login
			if (!$pparams->get('admin_login', false)) continue;

			// If it's the default hubzero plugin, don't include it in the list (we'll include it separately)
			if ($p->name == 'hubzero')
			{
				$site_display = $pparams->get('display_name', \Config::get('sitename'));
				$basic = true;
			}
			else
			{
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[$p->name] = array('name' => $p->name, 'display' => $display);
			}
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get an HTML select list of the available languages.
	 *
	 * @return  string
	 */
	public static function getLanguageList()
	{
		$languages = array();
		$languages = Lang::getList(null, JPATH_ADMINISTRATOR, false, true);
		array_unshift($languages, Html::select('option', '', Lang::txt('JDEFAULT')));

		return Html::select('genericlist', $languages, 'lang', ' class="inputbox"', 'value', 'text', null);
	}

	/**
	 * Get the redirect URI after login.
	 *
	 * @return  string
	 */
	public static function getReturnURI()
	{
		$return = 'index.php?' . Request::getQueryString();
		if ($return != 'index.php?option=com_login')
		{
			return base64_encode($return);
		}

		return base64_encode('index.php');
	}
}
