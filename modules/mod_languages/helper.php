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

namespace Modules\Languages;

use Hubzero\Module\Module;
use JLanguageHelper;
use JLanguage;
use JFactory;
use JLoader;
use Route;
use JString;
use MenusHelper;
use User;

/**
 * Module class for displaying a menu
 */
class Helper extends Module
{
	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy compatibility
		$params = $this->params;
		$module = $this->module;

		$headerText	= JString::trim($params->get('header_text'));
		$footerText	= JString::trim($params->get('footer_text'));

		$list = self::getList($params);

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of languages
	 *
	 * @param   object  $params  JRegistry The module options.
	 * @return  array
	 */
	public static function getList(&$params)
	{
		JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$user = User::getRoot();
		$lang = JFactory::getLanguage();
		$app  = JFactory::getApplication();
		$menu = $app->getMenu();

		// Get menu home items
		$homes = array();
		foreach ($menu->getMenu() as $item)
		{
			if ($item->home)
			{
				$homes[$item->language] = $item;
			}
		}

		// Load associations
		$assoc = isset($app->menu_associations) ? $app->menu_associations : 0;
		if ($assoc)
		{
			$active = $menu->getActive();
			if ($active)
			{
				$associations = MenusHelper::getAssociations($active->id);
			}
		}

		$levels    = $user->getAuthorisedViewLevels();
		$languages = JLanguageHelper::getLanguages();

		// Filter allowed languages
		foreach ($languages as $i => &$language)
		{
			// Do not display language without frontend UI
			if (!JLanguage::exists($language->lang_code))
			{
				unset($languages[$i]);
			}
			// Do not display language without specific home menu
			elseif (!isset($homes[$language->lang_code]))
			{
				unset($languages[$i]);
			}
			// Do not display language without authorized access level
			elseif (isset($language->access) && $language->access && !in_array($language->access, $levels))
			{
				unset($languages[$i]);
			}
			else
			{
				$language->active = $language->lang_code == $lang->getTag();
				if ($app->getLanguageFilter())
				{
					if (isset($associations[$language->lang_code]) && $menu->getItem($associations[$language->lang_code]))
					{
						$itemid = $associations[$language->lang_code];
						if ($app->getCfg('sef')=='1')
						{
							$language->link = Route::url('index.php?lang=' . $language->sef . '&Itemid=' . $itemid);
						}
						else
						{
							$language->link = 'index.php?lang=' . $language->sef . '&amp;Itemid=' . $itemid;
						}
					}
					else
					{
						if ($app->getCfg('sef')=='1')
						{
							$itemid = isset($homes[$language->lang_code]) ? $homes[$language->lang_code]->id : $homes['*']->id;
							$language->link = Route::url('index.php?lang=' . $language->sef . '&Itemid=' . $itemid);
						}
						else
						{
							$language->link = 'index.php?lang=' . $language->sef;
						}
					}
				}
				else
				{
					$language->link = Route::url('&Itemid=' . $homes['*']->id);
				}
			}
		}
		return $languages;
	}
}
