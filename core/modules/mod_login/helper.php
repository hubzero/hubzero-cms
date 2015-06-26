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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Login;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Plugin;
use JFactory;
use JURI;
use Request;
use User;

/**
 * Module class for displaying a login form
 */
class Helper extends Module
{
	/**
	 * Get the redirect URL
	 *
	 * @param   object  $params  Registry The module options.
	 * @param   string  $type    Type
	 * @return  string
	 */
	static function getReturnURL($params, $type)
	{
		$app    = JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		if ($itemid =  $params->get($type))
		{
			$db = App::get('db');
			$query = $db->getQuery(true);

			$query->select($db->quoteName('link'));
			$query->from($db->quoteName('#__menu'));
			$query->where($db->quoteName('published') . '=1');
			$query->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult())
			{
				if ($router->getMode() == JROUTER_MODE_SEF)
				{
					$url = 'index.php?Itemid=' . $itemid;
				}
				else
				{
					$url = $link . '&Itemid=' . $itemid;
				}
			}
		}

		if (!$url)
		{
			// stay on the same page
			$uri = clone JFactory::getURI();
			$vars = $router->parse($uri);
			unset($vars['lang']);

			if ($router->getMode() == JROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = \App::get('menu');
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query)
					{
						$url = 'index.php?Itemid=' . $itemid;
					}
					else
					{
						$url = 'index.php?' . JURI::buildQuery($vars) . '&Itemid=' . $itemid;
					}
				}
				else
				{
					$url = 'index.php?' . JURI::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?' . JURI::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}

	/**
	 * Get type
	 *
	 * @return  string
	 */
	static function getType()
	{
		return (!User::isGuest()) ? 'logout' : 'login';
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy compatibility for older view overrides
		$params = $this->params;
		$module = $this->module;

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			\App::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		$this->css('login.css', 'com_users');
		$this->css('providers.css', 'com_users');
		$this->js('login', 'com_users');

		$this->css('uniform.css', 'system');
		$this->js('jquery.uniform', 'system');
		$this->js('jquery.hoverIntent', 'system');

		$type    = self::getType();
		$return	 = Request::getVar('return', null);
		$freturn = base64_encode($_SERVER['REQUEST_URI']);

		// If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if ($areturn = Request::getVar('return', null))
		{
			$areturn = base64_decode($areturn);
			$query   = parse_url($areturn);
			if (is_array($query) && isset($query['query']))
			{
				$query  = $query['query'];
				$query  = explode('&', $query);
				$auth   = '';
				foreach ($query as $q)
				{
					$n = explode('=', $q);
					if ($n[0] == 'authenticator')
					{
						$auth = $n[1];
					}
				}
			}
		}

		// Figure out whether or not any of our third party auth plugins are turned on
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$plugins        = Plugin::byType('authentication');
		$authenticators = array();

		foreach ($plugins as $p)
		{
			if ($p->name != 'hubzero' && $p->name != $auth)
			{
				$pparams = new Registry($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[] = array('name' => $p->name, 'display' => $display);
				$multiAuth = true;
			}
			else if ($p->name == 'hubzero')
			{
				$pparams = new Registry($p->params);
				$remember_me_default = $pparams->get('remember_me_default', 0);
			}
		}

		Plugin::import('authentication');

		// Set the return if we have it...
		$returnQueryString = ($return) ? "&return={$return}" : '';

		require $this->getLayoutPath();
	}
}