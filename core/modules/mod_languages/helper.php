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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
