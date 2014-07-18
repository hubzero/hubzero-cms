<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modLoginHelper
{
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
			if ($link = $db->loadResult()) {
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
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
					if (isset($item) && $vars == $item->query) {
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JURI::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JURI::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JURI::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}

	static function getType()
	{
		$user = JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}

	/**
	 * Display module content
	 *
	 * @return     void
	 */
	static function display($params, $module)
	{
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
		$return	 = JRequest::getVar('return', null);
		$freturn = base64_encode($_SERVER['REQUEST_URI']);

		// If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if ($areturn = JRequest::getVar('return', null))
		{
			$areturn = base64_decode($areturn);
			$query   = parse_url($areturn);
			$query   = $query['query'];
			$query   = explode('&', $query);
			$auth    = '';
			foreach ($query as $q)
			{
				$n = explode('=', $q);
				if ($n[0] == 'authenticator')
				{
					$auth = $n[1];
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

		// Set the return if we have it...
		$r = ($return) ? "&return={$return}" : '';

		require(JModuleHelper::getLayoutPath($module->module));
	}
}