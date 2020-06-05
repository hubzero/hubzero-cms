<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * Redirect to Member Home Page
 */
class plgSystemMemberHome extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to catch the onAfterRoute event.
	 *
	 * @return  boolean
	 */
	public function onAfterRoute()
	{
		$task = Request::getCmd('task', 'none');

		if (User::isGuest() || !App::isSite() || $task == 'user.logout')
		{
			return false;
		}

		$menuId = $this->params->get('menuId', 0);

		if (!$menuId)
		{
			return false;
		}

		$menu = App::get('menu');
		$activeMenu  = $menu->getActive();
		$defaultMenu = $menu->getDefault();

		// If routing to the home page...
		if ($activeMenu == $defaultMenu)
		{
			// Reset the active menu item and
			// overwrite request vars
			$menu->setActive($menuId);
			$menu->setDefault($menuId, $defaultMenu->language);

			$item = $menu->getItem($menuId);
			$vars = $item->query;
			$vars['Itemid'] = $menuId;

			foreach ($vars as $key => $var)
			{
				Request::setVar($key, $var);
			}
		}

		return true;
	}
}
