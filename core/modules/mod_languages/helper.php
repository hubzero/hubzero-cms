<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Languages;

use Hubzero\Module\Module;
use MenusHelper;
use Config;
use Route;
use User;
use Lang;
use App;

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

		$headerText = trim($params->get('header_text'));
		$footerText = trim($params->get('footer_text'));

		$list = self::getList($params);

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of languages
	 *
	 * @param   object  $params  Registry The module options.
	 * @return  array
	 */
	public static function getList(&$params)
	{
		require_once \Component::path('com_menus') . '/admin/helpers/menus.php';

		$lang = Lang::getRoot();
		$menu = App::get('menu');

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

		$levels    = User::getAuthorisedViewLevels();
		$languages = Lang::available();

		// Filter allowed languages
		foreach ($languages as $i => &$language)
		{
			// Do not display language without frontend UI
			if (!Lang::exists($language->lang_code, PATH_APP . DS . 'bootstrap' . DS . strtolower(App::get('client')->name))
			 && !Lang::exists($language->lang_code, PATH_CORE . DS . 'bootstrap' . DS . ucfirst(App::get('client')->name)))
			{
				unset($languages[$i]);
			}
			// Do not display language without specific home menu
			/*elseif (!isset($homes[$language->lang_code]))
			{
				unset($languages[$i]);
			}*/
			// Do not display language without authorized access level
			elseif (isset($language->access) && $language->access && !in_array($language->access, $levels))
			{
				unset($languages[$i]);
			}
			else
			{
				$language->active = $language->lang_code == $lang->getTag();

				if (App::get('language.filter'))
				{
					if (isset($associations[$language->lang_code]) && $menu->getItem($associations[$language->lang_code]))
					{
						$itemid = $associations[$language->lang_code];
						if (Config::get('sef'))
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
						if (Config::get('sef'))
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
