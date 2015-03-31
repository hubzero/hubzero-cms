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
use JPluginHelper;
use JFactory;
use JRegistry;
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
	 * @param   object  $params  JRegistry The module options.
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
			$db = JFactory::getDbo();
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
					$menu = $app->getMenu();
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

		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			$app->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'login.css');
		\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'providers.css');
		\Hubzero\Document\Assets::addComponentScript('com_users', 'assets/js/login');

		\Hubzero\Document\Assets::addSystemStylesheet('uniform.css');
		\Hubzero\Document\Assets::addSystemScript('jquery.uniform');
		\Hubzero\Document\Assets::addSystemScript('jquery.hoverIntent');

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
		$plugins        = JPluginHelper::getPlugin('authentication');
		$authenticators = array();

		foreach ($plugins as $p)
		{
			if ($p->name != 'hubzero' && $p->name != $auth)
			{
				$pparams = new JRegistry($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[] = array('name' => $p->name, 'display' => $display);
				$multiAuth = true;
			}
			else if ($p->name == 'hubzero')
			{
				$pparams = new JRegistry($p->params);
				$remember_me_default = $pparams->get('remember_me_default', 0);
			}
		}

		JPluginHelper::importPlugin('authentication');

		// Set the return if we have it...
		$returnQueryString = ($return) ? "&return={$return}" : '';

		require $this->getLayoutPath();
	}
}